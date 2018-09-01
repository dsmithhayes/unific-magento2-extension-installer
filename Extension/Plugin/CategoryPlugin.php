<?php

namespace Unific\Extension\Plugin;

class CategoryPlugin extends AbstractPlugin
{
    protected $entity = 'category';
    protected $subject = 'category/create';

    /**
     * @param $subject
     * @param \Magento\Catalog\Api\Data\CategoryInterface $category
     * @return array
     */
    public function beforeSave($subject, \Magento\Catalog\Api\Data\CategoryInterface $category)
    {
        foreach ($this->getRequestCollection()
                     ->addFieldToFilter('request_event', array('eq' => 'Magento\Catalog\Api\CategoryManagementInterface::save'))
                     ->addFieldToFilter('request_event_execution', array('eq' => 'before'))
                 as $id => $request) {

            $this->handleCondition($id, $request, $category);
        }

        return [$subject];
    }

    /**
     * @param $subject
     * @param \Magento\Catalog\Api\Data\CategoryInterface $category
     * @return mixed
     */
    public function afterSave($subject, \Magento\Catalog\Api\Data\CategoryInterface $category)
    {
        foreach ($this->getRequestCollection()
                     ->addFieldToFilter('request_event', array('eq' => 'Magento\Catalog\Api\CategoryManagementInterface::place'))
                     ->addFieldToFilter('request_event_execution', array('eq' => 'after'))
                 as $id => $request) {

            $this->handleCondition($id, $request, $category);
        }

        return $category;
    }
}
