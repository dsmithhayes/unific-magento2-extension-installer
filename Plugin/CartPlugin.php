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
        $this->customer = $this->customerFactory->create();

        $this->address = $this->addressFactory->create();
        $this->address->setFirstname($address->getFirstname());
        $this->address->setMiddlename($address->getMiddlename());
        $this->address->setLastname($address->getLastname());
        $this->address->setStreet($address->getStreet());
        $this->address->setCity($address->getCity());
        $this->address->setCompany($address->getCompany());
        $this->address->setTelephone($address->getTelephone());
        $this->address->setPostcode($address->getPostcode());
        $this->address->setCountryId($address->getCountryId());
        $this->customer->setAddresses(array($this->address));

        $this->customer->setFirstname($address->getFirstname());
        $this->customer->setMiddlename($address->getMiddlename());
        $this->customer->setLastname($address->getLastname());

        $returnValue = $proceed($cartId, $address);

        $this->quote->setCustomerFirstname($address->getFirstname());
        $this->quote->setCustomerMiddlename($address->getMiddlename());
        $this->quote->setCustomerLastname($address->getLastname());
        $this->quote->setCustomerEmail($address->getEmail());

        if($address->getStreet())
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
