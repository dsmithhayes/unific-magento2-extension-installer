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

        $this->customerFactory->setFirstname($address->getFirstname());
        $this->customerFactory->setMiddlename($address->getMiddlename());
        $this->customerFactory->setLastname($address->getLastname());

        $returnValue = $proceed($cartId, $address);

        $data = $address->getData();

        $this->quote->setCustomerFirstname(isset($data['firstname']) ? $data['firstname'] : '');
        $this->quote->setCustomerMiddlename(isset($data['middlename']) ? $data['middlename'] : '');
        $this->quote->setCustomerLastname(isset($data['lastname']) ? $data['lastname'] : '');


        $this->quote->setCustomerEmail(isset($data['email']) ? $data['email'] : '');

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
