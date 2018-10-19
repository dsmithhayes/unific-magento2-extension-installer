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
    protected $customerRepository;

    protected $customerFactory;
    protected $addressFactory;
    protected $dataObjectFactory;
    protected $quoteFactory;

    protected $quote;
    protected $order;
    protected $invoice;
    protected $customer;
    protected $customerAddress;
    protected $address;
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
     * @param \Magento\Customer\Model\ResourceModel\CustomerRepository $customerRepository
     * @param \Magento\Customer\Model\Data\AddressFactory $addressFactory
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
        \Magento\Customer\Model\ResourceModel\CustomerRepository $customerRepository,
        \Magento\Customer\Model\Data\AddressFactory $addressFactory,
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
        $this->addressFactory = $addressFactory;
        $this->customerRepository = $customerRepository;
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
                    $dataToSend = $this->getWebhookData($actionData['webhook'], $extraData);

                    if($this->isWebhookComplete($actionData['webhook'], $dataToSend))
                    {
                        $response = $this->restConnection->{$actionData['method']}(
                            $actionData['request_url'],
                            $dataToSend,
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

    protected function isWebhookComplete($type = 'order', $data = array())
    {
        switch($type)
        {
            case 'customer':
                // Only send a customer which has an email set
                return (isset($data['email']) && $data['email'] != null);
            case 'checkout':
                return (isset($data['customer_email']) && $data['customer_email'] != null);
            default:
                return true;
        }
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

        try {
            if($this->customer != null) {
                if($this->customer->getId() == null) {
                    $returnData['entity_id'] = 0;
                    $returnData['customer_is_guest'] = 1;
                } else {
                    $returnData['entity_id'] = $this->customer->getId();
                    $returnData['customer_is_guest'] = 0;
                }

                if($this->customer->getEmail() == null)
                {
                    $subentity = (isset($this->order)) ? $this->order : $this->quote;

                    // Using the billing address because the item might not need shipping
                    if(isset($subentity) && $subentity->getBillingAddress() != null)
                    {
                        $returnData['email'] = $subentity->getBillingAddress()->getEmail();
                        $returnData['prefix'] = $subentity->getBillingAddress()->getPrefix();
                        $returnData['firstname'] = $subentity->getBillingAddress()->getFirstname();
                        $returnData['middlename'] = $subentity->getBillingAddress()->getMiddlename();
                        $returnData['lastname'] = $subentity->getBillingAddress()->getLastname();
                        $returnData['suffix'] = $subentity->getBillingAddress()->getSuffix();
                        $returnData['dob'] = $subentity->getBillingAddress()->getDob();
                        $returnData['gender'] = $subentity->getBillingAddress()->getGender();
                        $returnData['created_at'] = date('Y-m-d H:i:s');
                        $returnData['updated_at'] = date('Y-m-d H:i:s');

                        $returnData['addresses'] = array();

                        $returnData['addresses'][] = array(
                            'firstname' => $subentity->getBillingAddress()->getFirstname(),
                            'middlename' => $subentity->getBillingAddress()->getMiddlename(),
                            'lastname' => $subentity->getBillingAddress()->getLastname(),
                            'street' => (is_string($subentity->getBillingAddress()->getStreet()) ? explode('\n', $subentity->getBillingAddress()->getStreet()) : $subentity->getBillingAddress()->getStreet()),
                            'postcode' => $subentity->getBillingAddress()->getPostcode(),
                            'city' => $subentity->getBillingAddress()->getCity(),
                            'country' => $subentity->getBillingAddress()->getCountryId(),
                            'telephone' => $subentity->getBillingAddress()->getTelephone(),
                            'company' => $subentity->getBillingAddress()->getCompany()
                        );

                        if($subentity->getShippingAddress())
                        {
                            $returnData['addresses'][] = array(
                                'firstname' => $subentity->getShippingAddress()->getFirstname(),
                                'middlename' => $subentity->getShippingAddress()->getMiddlename(),
                                'lastname' => $subentity->getShippingAddress()->getLastname(),
                                'street' => (is_string($subentity->getShippingAddress()->getStreet()) ? explode('\n', $subentity->getBillingAddress()->getStreet()) : $subentity->getBillingAddress()->getStreet()),
                                'postcode' => $subentity->getShippingAddress()->getPostcode(),
                                'city' => $subentity->getShippingAddress()->getCity(),
                                'country' => $subentity->getShippingAddress()->getCountryId(),
                                'telephone' => $subentity->getShippingAddress()->getTelephone(),
                                'company' => $subentity->getShippingAddress()->getCompany()
                            );
                        }
                    }
                } else{
                    $returnData['email'] = $this->customer->getEmail();
                    $returnData['prefix'] = $this->customer->getPrefix();
                    $returnData['firstname'] = $this->customer->getFirstname();
                    $returnData['middlename'] = $this->customer->getMiddlename();
                    $returnData['lastname'] = $this->customer->getLastname();
                    $returnData['suffix'] = $this->customer->getSuffix();
                    $returnData['dob'] = $this->customer->getDob();
                    $returnData['gender'] = $this->customer->getGender();

                    $returnData['created_at'] = $this->customer->getCreatedAt();
                    $returnData['updated_at'] = $this->customer->getUpdatedAt();

                    if($returnData['created_at'] == null)
                    {
                        $returnData['created_at'] = date('Y-m-d H:i:s');
                    }

                    if($returnData['updated_at'] == null)
                    {
                        $returnData['updated_at'] = date('Y-m-d H:i:s');
                    }

                    $returnData['addresses'] = array();
                    $addresses = $this->customer->getAddresses();

                    foreach($addresses as $address)
                    {
                        $returnData['addresses'][] = array(
                            'firstname' => $address->getFirstname(),
                            'middlename' => $address->getMiddlename(),
                            'lastname' => $address->getLastname(),
                            'street' => (is_string($address->getStreet()) ? explode('\n', $address->getStreet()) : $address->getStreet()),
                            'postcode' => $address->getPostcode(),
                            'city' => $address->getCity(),
                            'country' => $address->getCountryId(),
                            'telephone' => $address->getTelephone(),
                            'company' => $address->getCompany()
                        );
                    }
                }
            }
        } catch(\Exception $e)
        {
            $this->logger->error('Could not set customer info: ' . $e->getMessage());
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

        try {
            if($this->order != null)
            {
                $returnData['customer_email'] = $this->order->getCustomerEmail();
                $returnData['customer_firstname'] = $this->order->getCustomerFirstname();
                $returnData['customer_middlename'] = $this->order->getCustomerMiddlename();
                $returnData['customer_lastname'] = $this->order->getCustomerLastname();
            }

            $returnData['items'] = array();
            foreach($this->quote->getAllItems() as $item)
            {
                $returnData['items'][] = $item->getData();
            }
        } catch(\Exception $e)
        {
            $this->logger->error('Could not set cart info: ' . $e->getMessage());
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

        $returnData = array();

        try {
            $returnData = $this->order->getData();

            $returnData['customer_is_guest'] = (int) $returnData['customer_is_guest'];

            $returnData['order_items'] = array();
            foreach($this->order->getAllItems() as $item)
            {
                $itemData = $item->getData();
                $itemData['free_shipping'] = (isset($itemData['free_shipping']) && $itemData['free_shipping'] == true) ? 1 : 0;

                if(isset($itemData['is_qty_decimal']))
                {
                    $itemData['is_qty_decimal'] = (int)$itemData['is_qty_decimal'];
                }

                if(isset($itemData['qty_ordered']))
                {
                    $itemData['qty_ordered'] = (int)$itemData['qty_ordered'];
                }

                if(isset($itemData['qty_canceled']))
                {
                    $itemData['qty_canceled'] = (int)$itemData['qty_canceled'];
                }

                if(isset($itemData['qty_invoiced']))
                {
                    $itemData['qty_invoiced'] = (int)$itemData['qty_invoiced'];
                }

                if(isset($itemData['qty_refunded']))
                {
                    $itemData['qty_refunded'] = (int)$itemData['qty_refunded'];
                }

                if(isset($itemData['qty_shipped']))
                {
                    $itemData['qty_shipped'] = (int)$itemData['qty_shipped'];

                }

                $returnData['order_items'][] = $itemData;
            }

            $returnData['addresses'] = array();
            $returnData['addresses']['billing'] = $this->order->getBillingAddress()->getData();
            $returnData['addresses']['shipping'] = $this->order->getShippingAddress()->getData();
            $returnData['payment'] = $this->order->getPayment()->getData();
        } catch (\Exception $e)
        {
            $this->logger->info('Could not set order info: ' . $e->getMessage());
        }

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