<?php

namespace Unific\Extension\Plugin;

class CustomerSessionPlugin extends AbstractPlugin
{
    protected $entity = 'customer';

    /**
     * @param $subject
     * @param callable $proceed
     * @param $customer
     * @return array
     */
    public function aroundSetCustomerDataAsLoggedIn($subject, callable $proceed, $customer)
    {
        $this->subject = 'customer/login';

        foreach ($this->getRequestCollection('before') as $request)
        {
            $this->handleConditions($request->getId(), $request);
        }

        $result = $proceed($customer);

        foreach ($this->getRequestCollection() as $request)
        {
            $this->handleConditions($request->getId(), $request);
        }

        return $result;
    }

    /**
     * @param $subject
     * @param callable $proceed
     * @return array
     */
    public function aroundLogout($subject, callable $proceed)
    {
        $this->subject = 'customer/logout';

        foreach ($this->getRequestCollection('before') as $request)
        {
            $this->handleConditions($request->getId(), $request);
        }

        $result = $proceed();

        foreach ($this->getRequestCollection() as $request)
        {
            $this->handleConditions($request->getId(), $request);
        }

        return $result;
    }
}
