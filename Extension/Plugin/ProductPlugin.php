<?php

namespace Unific\Extension\Plugin;

class ProductPlugin extends AbstractPlugin
{
    protected $entity = 'product';
    protected $subject = 'product/create';

    /**
     * @param $subject
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param bool $saveOptions
     * @return array
     */
    public function beforeSave($subject, \Magento\Catalog\Api\Data\ProductInterface $product, $saveOptions = false)
    {
        foreach ($this->getRequestCollection()
                     ->addFieldToFilter('request_event', array('eq' => 'Magento\Catalog\Api\ProductManagementInterface::save'))
                     ->addFieldToFilter('request_event_execution', array('eq' => 'before'))
                 as $id => $request) {

            $this->handleCondition($id, $request, $product);
        }

        return [$product, $saveOptions];
    }

    /**
     * @param $subject
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param bool $saveOptions
     * @return mixed
     */
    public function afterSave($subject, \Magento\Catalog\Api\Data\ProductInterface $product, $saveOptions = false)
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
