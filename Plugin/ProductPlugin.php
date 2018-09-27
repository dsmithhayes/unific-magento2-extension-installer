<?php

namespace Unific\Extension\Plugin;

class ProductPlugin extends AbstractPlugin
{
    protected $entity = 'product';
    protected $subject = 'product/create';

    /**
     * @param \Magento\Catalog\Model\Product $subject
     * @param callable $proceed
     * @return array
     */
    public function aroundSave(\Magento\Catalog\Model\Product $subject, callable $proceed)
    {
        $this->setSubject($subject);
        $this->product = $subject;

        foreach ($this->getRequestCollection('before') as $request)
        {
            $this->handleConditions($request->getId(), $request);
        }

        $result = $proceed();

        foreach ($this->getRequestCollection() as $request)
        {
            $this->handleConditions($request->getId(), $request);
        }

        return $result;
    }

    public function setSubject($product)
    {
        if($product->getCreatedAt() != $product->getUpdatedAt())
        {
            $this->subject = 'product/update';
        }
    }
}
