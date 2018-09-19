<?php

namespace Unific\Extension\Plugin;

class CustomerPlugin extends AbstractPlugin
{
    protected $entity = 'customer';
    protected $subject = 'customer/create';

    protected $customerRepository;

    /**
     * OrderPlugin constructor.
     * @param \Unific\Extension\Logger\Logger $logger
     * @param \Unific\Extension\Helper\Mapping $mapping
     * @param \Unific\Extension\Connection\Rest\Connection $restConnection
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        \Unific\Extension\Logger\Logger $logger,
        \Unific\Extension\Helper\Mapping $mapping,
        \Unific\Extension\Connection\Rest\Connection $restConnection,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    )
    {
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $this->logger = $logger;
        $this->mappingHelper = $mapping;
        $this->restConnection = $restConnection;
        $this->customerRepository = $customerRepository;

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
            $this->handleCondition($request->getId(), $request, $this->getCustomerInfo($this->customerRepository->getById($customer->getId())));
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
            $this->setSubject($customer);
            $this->handleCondition($request->getId(), $request, $this->getCustomerInfo($this->customerRepository->getById($customer->getId())));
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

        $returnData['billing_address'] = $customer->getDefaultBilling()->getData();
        $returnData['shipping_address'] = $customer->getDefaultShipping()->getData();

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
