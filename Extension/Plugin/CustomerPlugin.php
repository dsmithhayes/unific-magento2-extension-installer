<?php

namespace Unific\Extension\Plugin;

class CustomerPlugin extends AbstractPlugin
{
    protected $entity = 'customer';
    protected $subject = 'customer/create';

    /**
     * @param $subject
     * @param \Magento\Customer\Model\Customer $customer
     * @return array
     */
    public function beforeSave($subject, \Magento\Customer\Model\Customer $customer)
    {
        foreach ($this->getRequestCollection()
                     ->addFieldToFilter('request_event', array('eq' => 'Magento\Customer\Model\Customer::save'))
                     ->addFieldToFilter('request_event_execution', array('eq' => 'before'))
                 as $id => $request) {

            $this->handleCondition($id, $request, $customer);
        }

        return [$customer];
    }

    /**
     * @param $subject
     * @param \Magento\Customer\Model\Customer $customer
     * @return mixed
     */
    public function afterSave($subject, \Magento\Customer\Model\Customer $customer)
    {
        foreach ($this->getRequestCollection()
                     ->addFieldToFilter('request_event', array('eq' => 'Magento\Customer\Model\Customer::save'))
                     ->addFieldToFilter('request_event_execution', array('eq' => 'after'))
                 as $id => $request) {

            $this->handleCondition($id, $request, $customer);
        }

        return $customer;
    }
}
