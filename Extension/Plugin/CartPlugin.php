<?php

namespace Unific\Extension\Plugin;

class CartPlugin extends AbstractPlugin
{
    protected $entity = 'cart';
    protected $subject = 'checkout/create';

    /**
     * @param $subject
     * @param $email
     * @return array
     */
    public function beforeIsEmailAvailable($subject, $email)
    {
        foreach ($this->getRequestCollection('Magento\Customer\Model\AccountManagement::isEmailAvailable', 'before') as $request)
        {
            $this->handleCondition($request->getId(), $request, array('email' => $email));
        }

        return [$email];
    }

    /**
     * @param $subject
     * @param $email
     * @return mixed
     */
    public function afterIsEmailAvailable($subject, $email)
    {
        foreach ($this->getRequestCollection('Magento\Customer\Model\AccountManagement::isEmailAvailable') as $request)
        {
            $this->handleCondition($request->getId(), $request, array('email' => $email));
        }

        return $email;
    }
}
