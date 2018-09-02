<?php

namespace Unific\Extension\Plugin;

class CartPlugin extends AbstractPlugin
{
    protected $entity = 'cart';
    protected $subject = 'cart/create';

    /**
     * @param $subject
     * @param $quote
     * @return array
     */
    public function beforeSave($subject, $quote)
    {
        foreach ($this->getRequestCollection('Magento\Quote\Api\CartManagementInterface::save', 'before') as $request)
        {
            $this->handleCondition($request->getId(), $request, $quote);
        }

        return [$quote];
    }

    /**
     * @param $subject
     * @param $quote
     * @return mixed
     */
    public function afterSave($subject, $quote)
    {
        foreach ($this->getRequestCollection('Magento\Quote\Api\CartManagementInterface::save') as $request)
        {
            $this->handleCondition($request->getId(), $request, $quote);
        }

        return $quote;
    }
}
