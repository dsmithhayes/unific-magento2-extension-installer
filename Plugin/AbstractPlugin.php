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
    protected $customerFactory;
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
     * @param \Magento\Customer\Model\Data\CustomerFactory $customerFactory
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
        \Magento\Customer\Model\Data\CustomerFactory $customerFactory,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory
    )
    {
        $this->collectionFactory = $collectionFactory;
        $this->restConnection = $restConnection;
        $this->mappingHelper = $mapping;
        $this->logger = $logger;
        $this->requestFactory = $requestFactory;

        $this->orderRepository = $orderRepository;
        $this->customerRegistry = $customerRegistry;
        $this->customerFactory = $customerFactory;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->quoteFactory = $quoteFactory;
    }

    /**
     * @param string $eventExecution
     * @return mixed
     */
    public function getRequestCollection($eventExecution = 'after')
    {
        return $this->collectionFactory->create()
            ->addFieldToFilter('request_event', array('eq' => $this->subject))
            ->addFieldToFilter('request_event_execution', array('eq' => $eventExecution))->load();
    }

    /**
     * @param $id
     * @param $request
     */
    public function handleConditions($id, $request, $extraData = array())
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

                try {
                    $response = $this->restConnection->{$actionData['method']}(
                        $actionData['request_url'],
                        $this->getWebhookData($actionData['webhook'], $extraData),
                        array(
                            'X-SUBJECT' => $this->getWebhookSubject($actionData)
                        )
                    );

                    if(is_string($response))
                    {
                        $this->logger->info('Message queued for sending: ' . $response);
                    } else {
                        $this->logger->info($response->getBody());
                    }

                } catch(\Exception $e)
                {
                    $this->logger->error('Exception ' . $e->getCode() . ': ' . $e->getMessage());
                }

            }
        }
    }

    /**
     * @param array $actionData
     * @return string
     */
    protected function getWebhookSubject(array $actionData)
    {
        return ($this->isEventEntity($actionData)) ? $this->subject : $actionData['webhook'] . '/update';
    }

    /**
     * @param array $actionData
     * @return bool
     */
    protected function isEventEntity(array $actionData)
    {
        return $actionData['webhook'] == $this->entity;
    }

    /**
     * @param string $type
     * @return array|mixed
     */
    protected function getWebhookData($type = 'order', $extraData = array())
    {
        switch($type)
        {
            case 'customer':
                return array_merge($this->getCustomerInfo(), $extraData);
            case 'invoice':
                return array_merge($this->getInvoiceInfo(), $extraData);
            case 'checkout':
                return array_merge($this->getCartInfo(), $extraData);
            case 'product':
                return array_merge($this->getProductInfo(), $extraData);
            case 'category':
                return array_merge($this->getCategoryInfo(), $extraData);
            default:
                return array_merge($this->getOrderInfo(), $extraData);
        }
    }

    /**
     * @return array
     */
    protected function getProductInfo()
    {
        if($this->product == null) return array();

        $returnData = $this->product->getData();

        return $returnData;
    }

    /**
     * @return array
     */
    protected function getCategoryInfo()
    {
        if($this->category == null) return array();

        $returnData = $this->category->getData();

        return $returnData;
    }

    /**
     * @return mixed
     */
    protected function getCustomerInfo()
    {
        $returnData = array();

        if($this->customer != null) {
            $returnData = $this->customer->getData();

            if($this->customer->getId() == null) {
                $returnData['entity_id'] = 0;
                $returnData['customer_is_guest'] = 1;
                $returnData['created_at'] = $this->customer->getCreatedAt();
                $returnData['updated_at'] = $this->customer->getUpdatedAt();
            }

            $returnData['addresses'] = array();
            foreach($this->customer->getAddresses() as $address)
            {
                $returnData['addresses'][] = $address->getData();
            }

            if(isset($returnData['rp_token']))
            {
                unset($returnData['rp_token']);
            }

            if(isset($returnData['rp_token_created_at']))
            {
                unset($returnData['rp_token_created_at']);
            }
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

        if($this->order != null)
        {
            $returnData['customer_email'] = $this->order->getCustomerEmail();
        }

        $returnData['items'] = array();
        foreach($this->quote->getAllItems() as $item)
        {
            $returnData['items'][] = $item->getData();
        }

        return $returnData;
    }

    /**
     * @return array
     */
    protected function getInvoiceInfo()
    {
        if($this->invoice == null) return array();

        $returnData = $this->invoice->getData();

        return $returnData;
    }

    /**
     * @return mixed
     */
    protected function getOrderInfo()
    {
        if($this->order == null) return array();

        $returnData = $this->order->getData();

        $returnData['order_items'] = array();
        foreach($this->order->getAllItems() as $item)
        {
            $itemData = $item->getData();
            $itemData['free_shipping'] = (isset($itemData['free_shipping']) && $itemData['free_shipping'] == true) ? 1 : 0;
            $returnData['order_items'][] = $itemData;
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