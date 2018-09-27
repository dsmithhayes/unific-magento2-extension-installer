<?php

namespace Unific\Extension\Plugin;

class AbstractPlugin
{
    protected $logger;
    protected $mappingHelper;

    protected $restConnection;

    protected $collectionFactory;
    protected $requestFactory;

    protected $entity = 'order';
    protected $subject = 'order/create';

    protected $orderRepository;
    protected $customerRegistry;
    protected $dataObjectFactory;
    protected $quoteFactory;

    protected $quote;
    protected $order;
    protected $invoice;
    protected $customer;
    protected $customerAddress;
    protected $category;
    protected $product;
    protected $adminUser;

    /**
     * @param \Unific\Extension\Logger\Logger $logger
     * @param \Unific\Extension\Helper\Mapping $mapping
     * @param \Unific\Extension\Connection\Rest\Connection $restConnection
     * @param \Unific\Extension\Model\ResourceModel\Request\Grid\CollectionFactory $collectionFactory
     * @param \Unific\Extension\Model\RequestFactory $requestFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Customer\Model\CustomerRegistry $customerRegistry
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     */
    public function __construct(
        \Unific\Extension\Logger\Logger $logger,
        \Unific\Extension\Helper\Mapping $mapping,
        \Unific\Extension\Connection\Rest\Connection $restConnection,
        \Unific\Extension\Model\ResourceModel\Request\Grid\CollectionFactory $collectionFactory,
        \Unific\Extension\Model\RequestFactory $requestFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory
    )
    {
        $this->orderRepository = $orderRepository;
        $this->customerRegistry = $customerRegistry;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->quoteFactory = $quoteFactory;
    }

    /**
     * @param string $eventFilter
     * @param string $eventExecution
     * @return mixed
     */
    public function getRequestCollection($eventExecution = 'after')
    {
        return $this->collectionFactory->create()
            ->addFieldToFilter('request_event', array('eq' => $this->subject))
            ->addFieldToFilter('request_event_execution', array('eq' => $this->subject))->load();
    }

    /**
     * @param $id
     * @param $request
     * @param $dataModel
     */
    public function handleConditions($id, $request)
    {
        // A plugin attaches the sub data
        $model = $this->requestFactory->create();
        $model->load($id);

        $data = $model->getData();

        foreach($data['request_conditions'] as $condition)
        {
            if($condition['condition_action'] == 'request')
            {
                $actionData = json_decode($condition['condition_action_params'], true);
                $response = $this->restConnection->{$actionData['method']}(
                    $actionData['request_url'],
                    $this->getWebhookData($condition['webhook']),
                    array(
                        'X-SUBJECT' => $this->subject
                    )
                );

                $this->logger->info($response->getBody());
            }
        }
    }

    /**
     * @param string $type
     * @return array|mixed
     */
    protected function getWebhookData($type = 'order')
    {
        switch($type)
        {
            case 'customer':
                return $this->getCustomerInfo();
            case 'invoice':
                return $this->getInvoiceInfo();
            case 'cart':
                return $this->getCartInfo();
            default:
                return $this->getOrderInfo();
        }
    }

    /**
     * @return mixed
     */
    protected function getCustomerInfo()
    {
        if($this->customer == null) return array();

        $returnData = $this->customer->getData();

        $returnData['addresses'] = array();
        foreach($this->customer->getAddresses() as $address)
        {
            $returnData['addresses'][] = $address->getData();
        }

        return $returnData;
    }

    /**
     * @return array
     */
    protected function getCartInfo()
    {
        if($this->quote == null) return array();

        $returnData = $this->quote->getData();

        return $returnData;
    }

    /**
     * @return mixed
     */
    protected function getOrderInfo()
    {
        if($this->order == null) return array();

        $returnData = $this->order->getData();

        $returnData['items'] = array();
        foreach($this->order->getAllItems() as $item)
        {
            $returnData['items'][] = $item->getData();
        }

        $returnData['addresses'] = array();
        $returnData['addresses']['billing'] = $this->order->getBillingAddress()->getData();
        $returnData['addresses']['shipping'] = $this->order->getShippingAddress()->getData();
        $returnData['payment'] = $this->order->getPayment()->getData();

        return $returnData;
    }

    /**
     * @param array $data
     * @param string $eventExecution
     */
    protected function handleWebhook(array $data, $eventExecution = 'after')
    {
        foreach ($this->getRequestCollection($eventExecution) as $request)
        {
            $this->handleConditions($request->getId(), $request,  $data);
        }
    }

    /**
     * @param $order
     */
    protected function setSubject($order)
    {
    }
}