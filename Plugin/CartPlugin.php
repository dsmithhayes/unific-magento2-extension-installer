<?php

namespace Unific\Extension\Plugin;

class CartPlugin extends AbstractPlugin
{
    protected $entity = 'cart';
    protected $subject = 'cart/create';

    /**
     * @param $subject
     * @param callable $proceed
     * @param $email
     * @param null $websiteId
     * @return array
     */
    public function aroundIsEmailAvailable($subject, callable $proceed, $email, $websiteId = null)
    {
        $emailObject = $this->dataObjectFactory->create();
        $emailObject->setEmail($email);

        // Workaround, @todo
        $this->quote = $emailObject;

        foreach ($this->getRequestCollection('before') as $request)
        {
            $this->handleConditions($request->getId(), $request);
        }

        $returnValue = $proceed($email, $websiteId);

        foreach ($this->getRequestCollection() as $request)
        {
            $this->handleConditions($request->getId(), $request);
        }

        return $returnValue;
    }
}
