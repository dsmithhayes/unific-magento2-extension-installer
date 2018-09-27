<?php

namespace Unific\Extension\Plugin;

class CustomerPlugin extends AbstractPlugin
{
    protected $entity = 'customer';
    protected $subject = 'customer/create';

    /**
     * @param $subject
     * @param callable $proceed
     * @param $customer
     * @param null $passwordHash
     * @return array
     */
    public function aroundave($subject, callable $proceed, $customer, $passwordHash = null)
    {
        $this->setSubject($customer);

        foreach ($this->getRequestCollection('before') as $request)
        {
            if($customer && $customer->getId() != null)
            {
                $this->customer = $this->customerRegistry->retrieve($customer->getId());
                $this->handleConditions($request->getId(), $request);
            }

        }

        $result = $proceed($customer, $passwordHash);

        foreach ($this->getRequestCollection() as $request)
        {
            if($customer && $customer->getId() != null)
            {
                $this->customer = $this->customerRegistry->retrieve($customer->getId());
                $this->handleConditions($request->getId(), $request);
            }

        }

        return $result;
    }

    /**
     * @param $customer
     */
    protected function setSubject($customer)
    {
        if($customer->getCreatedAt() != $customer->getUpdatedAt())
        {
            $this->subject = 'customer/update';
        }
    }
}
