<?php

namespace Unific\Extension\Plugin;

class CustomerAddressPlugin extends AbstractPlugin
{
    protected $entity = 'address';
    protected $subject = 'customer/update';

    /**
     * @param $subject
     * @param callable $proceed
     * @param $address
     * @return array
     */
    public function aroundSave($subject, callable $proceed, $address)
    {
        if($address->getCustomerId()) {
            $this->customer = $this->customerRegistry->retrieve($address->getCustomerId());
            $this->setSubject($this->customer);
        }

        foreach ($this->getRequestCollection('before') as $request)
        {
            $this->handleConditions($request->getId(), $request);
        }

        $result = $proceed($address);

        foreach ($this->getRequestCollection() as $request)
        {
            $this->handleConditions($request->getId(), $request);
        }
        return $result;
    }

    protected function setSubject($customer)
    {
        if($customer->getCreatedAt() != $customer->getUpdatedAt())
        {
            $this->subject = 'customer/update';
        }
    }
}
