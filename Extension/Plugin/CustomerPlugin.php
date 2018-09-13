<?php

namespace Unific\Extension\Plugin;

class CustomerPlugin extends AbstractPlugin
{
    protected $entity = 'customer';
    protected $subject = 'customer/create';

    protected $customerFactory;

    /**
     * OrderPlugin constructor.
     * @param \Unific\Extension\Logger\Logger $logger
     * @param \Unific\Extension\Helper\Mapping $mapping
     * @param \Unific\Extension\Connection\Rest\Connection $restConnection
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerFactory
     */
    public function __construct(
        \Unific\Extension\Logger\Logger $logger,
        \Unific\Extension\Helper\Mapping $mapping,
        \Unific\Extension\Connection\Rest\Connection $restConnection,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerFactory
    )
    {
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $this->logger = $logger;
        $this->mappingHelper = $mapping;
        $this->restConnection = $restConnection;
        $this->customerFactory = $customerFactory;

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
        foreach ($this->getRequestCollection('Magento\Customer\Api\CustomerRepositoryInterface::save', 'before') as $request)
        {
            $this->setSubject($customer);
            $this->handleCondition($request->getId(), $request, $customer);
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
        foreach ($this->getRequestCollection('Magento\Customer\Api\CustomerRepositoryInterface::save') as $request)
        {
            $customerData = $this->customerFactory->create()->addFieldToFilter('entity_id', $customer->getId())->getFirstItem();

            $this->setSubject($customer);
            $this->handleCondition($request->getId(), $request, $customerData);
        }

        return $customer;
    }

    protected function setSubject($customer)
    {
        if($customer->getCreatedAt() != $customer->getUpdatedAt())
        {
            $this->subject = 'customer/update';
        }
    }
}
