<?php

namespace Unific\Extension\Plugin;

class CartPlugin extends AbstractPlugin
{
    protected $entity = 'checkout';
    protected $subject = 'checkout/create';

    /**
     * @param $subject
     * @param callable $proceed
     * @param $cartId
     * @param \Magento\Quote\Model\Quote\Address $address
     * @return array
     */
    public function aroundEstimateByExtendedAddress($subject, callable $proceed, $cartId, \Magento\Quote\Model\Quote\Address $address)
    {
        if(!$subject instanceof \Magento\Quote\Model\ShippingMethodManagement\Interceptor)
        {
            return $proceed($cartId, $address);
        }

        $this->quote = $this->quoteFactory->create()->load($cartId);

        $returnValue = $proceed($cartId, $address);

        $data = $address->getData();

        $this->quote->setCustomerFirstname($data['firstname']);
        $this->quote->setCustomerMiddlename(isset($data['middlename']) ? $data['middlename'] : '');
        $this->quote->setCustomerLastname($data['lastname']);
        $this->quote->setCustomerEmail($data['email']);

        if(isset($data['street']) && $data['street'] != null)
        {
            $addressData = array();
            $addressData['addresses'] = array();
            $addressData['addresses']['billing'] = $address->getData();
            $addressData['addresses']['shipping'] = $address->getData();

            foreach ($this->getRequestCollection() as $request)
            {
                $this->handleConditions($request->getId(), $request, $addressData);
            }
        }

        return $returnValue;
    }
}
