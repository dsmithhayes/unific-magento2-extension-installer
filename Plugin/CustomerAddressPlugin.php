<?php

namespace Unific\Extension\Plugin;

class CustomerAddressPlugin extends AbstractPlugin
{
    protected $entity = 'customer';
    protected $subject = 'customer/create';

    protected $customerRegistry;

    /**
     * @param \Unific\Extension\Logger\Logger $logger
     * @param \Unific\Extension\Helper\Mapping $mapping
     * @param \Unific\Extension\Connection\Rest\Connection $restConnection
     * @param \Unific\Extension\Model\ResourceModel\Request\Grid\CollectionFactory $collectionFactory
     * @param \Unific\Extension\Model\RequestFactory $requestFactory
     * @param \Magento\Customer\Model\CustomerRegistry $customerRegistry
     */
    public function __construct(
        \Unific\Extension\Logger\Logger $logger,
        \Unific\Extension\Helper\Mapping $mapping,
        \Unific\Extension\Connection\Rest\Connection $restConnection,
        \Unific\Extension\Model\ResourceModel\Request\Grid\CollectionFactory $collectionFactory,
        \Unific\Extension\Model\RequestFactory $requestFactory,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry
    )
    {
        $this->customerRegistry = $customerRegistry;

        parent::__construct($logger, $mapping, $restConnection, $collectionFactory, $requestFactory);
    }

    /**
     * @param $subject
     * @param $address
     * @return array
     */
    public function beforeSave($subject, $address)
    {
        $customerData = $this->customerRegistry->retrieve($address->getCustomerId());
        $this->setSubject($customerData);

        foreach ($this->getRequestCollection($this->subject, 'before') as $request)
        {
            $this->handleCondition($request->getId(), $request, $this->getCustomerInfo($customerData));
        }

        return [$address];
    }

    /**
     * @param $subject
     * @param $address
     * @return mixed
     */
    public function afterSave($subject, $address)
    {
        $customerData = $this->customerRegistry->retrieve($address->getCustomerId());
        $this->setSubject($customerData);

        foreach ($this->getRequestCollection($this->subject) as $request)
        {
            $this->handleCondition($request->getId(), $request, $this->getCustomerInfo($customerData));
        }

        return $address;
    }

    /**
     * @param $customer
     * @return mixed
     */
    protected function getCustomerInfo($customer)
    {
        $returnData = $customer->getData();

        $returnData['addresses'] = array();
        foreach($customer->getAddresses() as $address)
        {
            $returnData['addresses'][] = $address->getData();
        }

        return $returnData;
    }


    protected function setSubject($customer)
    {
        if($customer->getCreatedAt() != $customer->getUpdatedAt())
        {
            $this->subject = 'customer/update';
        }
    }
}
