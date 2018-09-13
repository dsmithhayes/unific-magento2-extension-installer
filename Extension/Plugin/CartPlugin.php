<?php

namespace Unific\Extension\Plugin;

class CartPlugin extends AbstractPlugin
{
    protected $entity = 'cart';
    protected $subject = 'cart/create';

    /**
     * @param $subject
     * @param $email
     * @param null $websiteId
     * @return array
     */
    public function beforeIsEmailAvailable($subject, $email, $websiteId = null)
    {
        foreach ($this->getRequestCollection('Magento\Customer\Api\AccountManagementInterface::isEmailAvailable', 'before') as $request)
        {
            $this->handleCondition($request->getId(), $request, array('email' => $email));
        }

        return [$email, $websiteId];
    }

    /**
     * @param $subject
     * @param $email
     * @param null $websiteId
     * @return mixed
     */
    public function afterIsEmailAvailable($subject, $email, $websiteId = null)
    {
        foreach ($this->getRequestCollection('Magento\Customer\Api\AccountManagementInterface::isEmailAvailable') as $request)
        {
            $this->handleCondition($request->getId(), $request, array('email' => $email));
        }

        return $email;
    }
}
