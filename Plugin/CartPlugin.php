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
     * @param \Magento\Quote\Model\Quote\Address $address
     * @return array
     */
    public function aroundEstimateByExtendedAddress($subject, callable $proceed, $cartId, \Magento\Quote\Model\Quote\Address $address)
    {
        $this->quote = $this->quoteFactory->create()->load($cartId);

        $returnValue = $proceed($cartId, $address);

        $addressData = array();
        $addressData['addresses'] = array();
        $addressData['addresses']['billing'] = $address->getData();
        $addressData['addresses']['shipping'] = $address->getData();

        foreach ($this->getRequestCollection() as $request)
        {
            $this->handleConditions($request->getId(), $request, $addressData);
        }

        return $returnValue;
    }
}
