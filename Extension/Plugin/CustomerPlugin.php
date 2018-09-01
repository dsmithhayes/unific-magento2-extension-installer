<?php

namespace Unific\Extension\Plugin;

class CustomerPlugin extends AbstractPlugin
{
    protected $entity = 'customer';
    protected $subject = 'customer/create';

    protected $customerManagement;

    /**
     * OrderPlugin constructor.
     * @param \Unific\Extension\Logger\Logger $logger
     * @param \Unific\Extension\Helper\Mapping $mapping
     * @param \Unific\Extension\Connection\Rest\Connection $restConnection
     * @param \Magento\Customer\Api\CustomerManagementInterface $customerManagement
     */
    public function __construct(
        \Unific\Extension\Logger\Logger $logger,
        \Unific\Extension\Helper\Mapping $mapping,
        \Unific\Extension\Connection\Rest\Connection $restConnection,
        \Magento\Customer\Api\CustomerManagementInterface $customerManagement
    )
    {
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $this->logger = $logger;
        $this->mappingHelper = $mapping;
        $this->restConnection = $restConnection;
        $this->customerManagement = $customerManagement;
    }

    /**
     * @param $subject
     * @param $customer
     * @param $password
     * @param $redirectUrl
     * @return array
     */
    public function beforeCreateAccount($subject, $customer, $password, $redirectUrl)
    {
        foreach ($this->getRequestCollection()
                     ->addFieldToFilter('request_event', array('eq' => 'Magento\Customer\Api\AccountManagementInterface::createAccount'))
                     ->addFieldToFilter('request_event_execution', array('eq' => 'before'))
                 as $id => $request) {


            $this->handleCondition($id, $request, $this->customerManagement->get($customer->getId()));
        }

        return [$customer, $password, $redirectUrl];
    }

    /**
     * @param $subject
     * @param $customer
     * @param $password
     * @param $redirectUrl
     * @return mixed
     */
    public function afterCreateAccount($subject, $customer, $password, $redirectUrl)
    {
        foreach ($this->getRequestCollection()
                     ->addFieldToFilter('request_event', array('eq' => 'Magento\Customer\Api\AccountManagementInterface::createAccount'))
                     ->addFieldToFilter('request_event_execution', array('eq' => 'after'))
                 as $id => $request) {

            $this->handleCondition($id, $request, $this->customerManagement->get($customer->getId()));
        }

        return $customer;
    }
}
