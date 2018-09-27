<?php

namespace Unific\Extension\Plugin;

class CartPlugin extends AbstractPlugin
{
    protected $entity = 'cart';
    protected $subject = 'cart/create';

    /**
     * @param $subject
     * @param callable $proceed
     * @param $cartId
     * @param \Magento\Quote\Api\Data\EstimateAddressInterface $address
     * @return array
     */
    public function aroundEstimateByExtendedAddress($subject, callable $proceed, $cartId, \Magento\Quote\Api\Data\EstimateAddressInterface $address)
    {
        $this->quote = $this->quoteFactory->create()->load($cartId);

        $returnValue = $proceed($cartId, $address);

        foreach ($this->getRequestCollection() as $request)
        {
            $this->handleConditions($request->getId(), $request, array('address' => $address->getData()));
        }

        return $returnValue;
    }
}
