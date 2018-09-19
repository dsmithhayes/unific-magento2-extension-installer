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
    public function beforeSetCustomerDataAsLoggedIn($subject, $customer)
    {
        $this->subject = 'customer/login';

        foreach ($this->getRequestCollection($this->subject, 'before') as $request)
        {
            $this->handleCondition($request->getId(), $request, $customer);
        }

        return [$customer];
    }

    /**
     * @param $subject
     * @param $customer
     * @return mixed
     */
    public function afterSetCustomerDataAsLoggedIn($subject, $customer)
    {
        $this->subject = 'customer/login';

        foreach ($this->getRequestCollection($this->subject) as $request)
        {
            $this->handleCondition($request->getId(), $request, $customer);
        }

        return $customer;
    }

    /**
     * @param $subject
     * @return array
     */
    public function beforeLogout($subject)
    {
        $this->subject = 'customer/logout';

        foreach ($this->getRequestCollection($this->subject, 'before') as $request)
        {
            $this->handleCondition($request->getId(), $request, $subject);
        }

        return [$subject];
    }

    /**
     * @param $subject
     * @return mixed
     */
    public function afterLogout($subject)
    {
        $this->subject = 'customer/logout';

        foreach ($this->getRequestCollection($this->subject) as $request)
        {
            $this->handleCondition($request->getId(), $request, $subject);
        }


        return $subject;
    }
}
