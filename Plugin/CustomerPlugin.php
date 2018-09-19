<?php

namespace Unific\Extension\Plugin;

class CustomerPlugin extends AbstractPlugin
{
    protected $entity = 'customer';
    protected $subject = 'customer/create';

    protected $customerRegistry;

    /**
     * OrderPlugin constructor.
     * @param \Unific\Extension\Logger\Logger $logger
     * @param \Unific\Extension\Helper\Mapping $mapping
     * @param \Unific\Extension\Connection\Rest\Connection $restConnection
     * @param \Magento\Customer\Model\CustomerRegistry $customerRegistry
     */
    public function __construct(
        \Unific\Extension\Logger\Logger $logger,
        \Unific\Extension\Helper\Mapping $mapping,
        \Unific\Extension\Connection\Rest\Connection $restConnection,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry
    )
    {
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $this->logger = $logger;
        $this->mappingHelper = $mapping;
        $this->restConnection = $restConnection;
        $this->customerRegistry = $customerRegistry;

        parent::__construct($logger, $mapping, $restConnection);
    }

    /**
     * @param $subject
     * @param $customer
     * @param null $passwordHash
     * @return array
     */
    public function beforeSave($subject, $customer, $passwordHash = null)
    {
        $this->setSubject($customer);

        foreach ($this->getRequestCollection($this->subject, 'before') as $request)
        {
            $customerData = $this->customerRegistry->retrieve($customer->getId());
            $this->handleCondition($request->getId(), $request, $this->getCustomerInfo($customerData));
        }

        return [$customer, $passwordHash];
    }

    /**
     * @param $subject
     * @param $customer
     * @param null $passwordHash
     * @return mixed
     */
    public function afterSave($subject, $customer, $passwordHash = null)
    {
        $this->setSubject($customer);

        foreach ($this->getRequestCollection($this->subject) as $request)
        {
            $customerData = $this->customerRegistry->retrieve($customer->getId());
            $this->handleCondition($request->getId(), $request, $this->getCustomerInfo($customerData));
        }

        return $customer;
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
