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
     * @param null $password
     * @param string $redirectUrl
     * @return array
     */
    public function beforeCreateAccount($subject, $customer, $password = null, $redirectUrl = '')
    {
        foreach ($this->getRequestCollection('Magento\Customer\Api\AccountManagementInterface::createAccount', 'before') as $request)
        {
            $this->handleCondition($request->getId(), $request, $customer);
        }

        return [$customer, $password, $redirectUrl];
    }

    /**
     * @param $subject
     * @param $customer
     * @param null $password
     * @param string $redirectUrl
     * @return mixed
     */
    public function afterCreateAccount($subject, $customer, $password = null, $redirectUrl = '')
    {
        foreach ($this->getRequestCollection('Magento\Customer\Api\AccountManagementInterface::createAccount') as $request)
        {
            $customerData = $this->customerFactory->create()->addFieldToFilter('entity_id', $customer->getId())->getFirstItem();
            $this->handleCondition($request->getId(), $request, $customerData);
        }

        return $customer;
    }
}
