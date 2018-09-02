<?php

namespace Unific\Extension\Plugin;

class CategoryPlugin extends AbstractPlugin
{
    protected $entity = 'category';
    protected $subject = 'category/create';

    /**
     * @param $subject
     * @return array
     */
    public function beforeSave($subject)
    {
        foreach ($this->getRequestCollection('Magento\Catalog\Model\Category::save', 'before') as $request)
        {
            $this->handleCondition($request->getId(), $request, $subject);
        }

        return [$subject];
    }

    /**
     * @param $subject
     * @return mixed
     */
    public function afterSave($subject)
    {
        foreach ($this->getRequestCollection('Magento\Catalog\Model\Category::save') as $request)
        {
            $this->handleCondition($request->getId(), $request, $subject);
        }

        return $subject;
    }
}
