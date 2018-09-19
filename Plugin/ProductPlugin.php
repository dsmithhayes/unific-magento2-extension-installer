<?php

namespace Unific\Extension\Plugin;

class ProductPlugin extends AbstractPlugin
{
    protected $entity = 'product';
    protected $subject = 'product/create';

    /**
     * @param \Magento\Catalog\Model\Product $subject
     * @return array
     */
    public function beforeSave(\Magento\Catalog\Model\Product $subject)
    {
        $this->setSubject($subject);

        foreach ($this->getRequestCollection($this->subject, 'before') as $request)
        {
            $this->handleCondition($request->getId(), $request, $subject);
        }
        return [$subject];
    }

    /**
     * @param \Magento\Catalog\Model\Product $subject
     * @return mixed
     */
    public function afterSave(\Magento\Catalog\Model\Product $subject)
    {
        $this->setSubject($subject);

        foreach ($this->getRequestCollection($this->subject) as $request)
        {
            $this->handleCondition($request->getId(), $request, $subject);
        }

        return $subject;
    }

    public function setSubject($product)
    {
        if($product->getCreatedAt() != $product->getUpdatedAt())
        {
            $this->subject = 'product/update';
        }
    }
}
