<?php

namespace Unific\Extension\Plugin;

class CartPlugin extends AbstractPlugin
{
    protected $entity = 'cart';
    protected $subject = 'checkout/create';

    /**
     * @param $subject
     * @param $productInfo
     * @param null $requestInfo
     * @return array
     */
    public function beforeAddProduct($subject, $productInfo, $requestInfo = null)
    {
        foreach ($this->getRequestCollection('Magento\Quote\Api\CartManagementInterface::save', 'before') as $request)
        {
            $this->handleCondition($request->getId(), $request, $productInfo);
        }

        return [$productInfo, $requestInfo];
    }

    /**
     * @param $subject
     * @param $productInfo
     * @param null $requestInfo
     * @return mixed
     */
    public function afterAddProduct($subject, $productInfo, $requestInfo = null)
    {
        foreach ($this->getRequestCollection('Magento\Quote\Api\CartManagementInterface::save') as $request)
        {
            $this->handleCondition($request->getId(), $request, $productInfo);
        }

        return [$productInfo, $requestInfo];
    }
}
