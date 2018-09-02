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
        foreach ($this->getRequestCollection('Magento\Catalog\Model\Product::save', 'before') as $request)
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
        foreach ($this->getRequestCollection('Magento\Catalog\Model\Product::save') as $request)
        {
            $this->handleCondition($request->getId(), $request, $subject);
        }

        return $subject;
    }
}
