<?php

namespace Unific\Extension\Plugin;

class CategoryPlugin extends AbstractPlugin
{
    protected $entity = 'category';
    protected $subject = 'category/create';

    /**
     * @param $subject
     * @param $category
     * @return array
     */
    public function beforeSave($subject, $category)
    {
        foreach ($this->getRequestCollection()
                     ->addFieldToFilter('request_event', array('eq' => 'Magento\Catalog\Api\CategoryManagementInterface::save'))
                     ->addFieldToFilter('request_event_execution', array('eq' => 'before'))
                 as $id => $request) {

            $this->handleCondition($id, $request, $category);
        }

        return [$category];
    }

    /**
     * @param $subject
     * @param $category
     * @return mixed
     */
    public function afterSave($subject, $category)
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
