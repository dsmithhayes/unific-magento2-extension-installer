<?php

namespace Unific\Extension\Plugin;

class ProductPlugin extends AbstractPlugin
{
    protected $entity = 'product';
    protected $subject = 'product/create';

    /**
     * @param $subject
     * @param $product
     * @return array
     */
    public function beforeSave($subject, $product)
    {
        foreach ($this->getRequestCollection()
                     ->addFieldToFilter('request_event', array('eq' => 'Magento\Catalog\Api\ProductManagementInterface::save'))
                     ->addFieldToFilter('request_event_execution', array('eq' => 'before'))
                 as $id => $request) {

            $this->handleCondition($id, $request, $product);
        }

        return [$product];
    }

    /**
     * @param $subject
     * @param $product
     * @return mixed
     */
    public function afterSave($subject, $product)
    {
        foreach ($this->getRequestCollection()
                     ->addFieldToFilter('request_event', array('eq' => 'Magento\Catalog\Api\ProductManagementInterface::place'))
                     ->addFieldToFilter('request_event_execution', array('eq' => 'after'))
                 as $id => $request) {

            $this->handleCondition($id, $request, $product);
        }

        return $product;
    }
}
