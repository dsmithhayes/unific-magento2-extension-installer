<?php

namespace Unific\Extension\Plugin;

class CustomerPlugin extends AbstractPlugin
{
    protected $entity = 'customer';
    protected $subject = 'customer/create';

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

            $this->handleCondition($id, $request, $customer);
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

            $this->handleCondition($id, $request, $customer);
        }

        return $customer;
    }
}
