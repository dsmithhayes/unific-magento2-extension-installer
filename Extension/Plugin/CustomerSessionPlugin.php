<?php

namespace Unific\Extension\Plugin;

class CustomerSessionPlugin extends AbstractPlugin
{
    protected $entity = 'customer';

    /**
     * @param $subject
     * @param $customer
     * @return array
     */
    public function beforeSetCustomerAsLoggedIn($subject, $customer)
    {
        $this->subject = 'customer/login';

        foreach ($this->getRequestCollection()
                     ->addFieldToFilter('request_event', array('eq' => 'Magento\Customer\Model\Session::setCustomerDataAsLoggedIn'))
                     ->addFieldToFilter('request_event_execution', array('eq' => 'before'))
                 as $id => $request) {

            $this->handleCondition($id, $request, $customer);
        }

        return [$customer];
    }

    /**
     * @param $subject
     * @param $customer
     * @return mixed
     */
    public function afterSetCustomerAsLoggedIn($subject, $customer)
    {
        $this->subject = 'customer/login';

        foreach ($this->getRequestCollection()
                     ->addFieldToFilter('request_event', array('eq' => 'Magento\Customer\Model\Session::setCustomerDataAsLoggedIn'))
                     ->addFieldToFilter('request_event_execution', array('eq' => 'after'))
                 as $id => $request) {

            $this->handleCondition($id, $request, $customer);
        }

        return $customer;
    }

    /**
     * @param $subject
     * @param $order
     * @return array
     */
    public function beforeLogout($subject, $customer)
    {
        $this->subject = 'customer/logout';

        foreach ($this->getRequestCollection()
                     ->addFieldToFilter('request_event', array('eq' => 'Magento\Customer\Model\Session::logout'))
                     ->addFieldToFilter('request_event_execution', array('eq' => 'before'))
                 as $id => $request) {

            $this->handleCondition($id, $request, $customer);
        }

        return [$customer];
    }

    /**
     * @param $subject
     * @param $order
     * @return mixed
     */
    public function afterLogout($subject, $customer)
    {
        $this->subject = 'customer/logout';

        foreach ($this->getRequestCollection()
                     ->addFieldToFilter('request_event', array('eq' => 'Magento\Customer\Model\Session::logout'))
                     ->addFieldToFilter('request_event_execution', array('eq' => 'after'))
                 as $id => $request) {

            $this->handleCondition($id, $request, $customer);
        }

        return $customer;
    }
}
