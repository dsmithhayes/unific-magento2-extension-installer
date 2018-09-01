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
        foreach ($this->getRequestCollection()
                     ->addFieldToFilter('request_event', array('eq' => 'Magento\Catalog\Api\ProductManagementInterface::save'))
                     ->addFieldToFilter('request_event_execution', array('eq' => 'before'))
                 as $id => $request) {

            $this->handleCondition($id, $request, $subject);
        }

        return [$subject];
    }

    /**
     * @param \Magento\Catalog\Model\Product $subject
     * @return mixed
     */
    public function afterSave(\Magento\Catalog\Model\Product $subject)
    {
        foreach ($this->getRequestCollection()
                     ->addFieldToFilter('request_event', array('eq' => 'Magento\Catalog\Api\ProductManagementInterface::place'))
                     ->addFieldToFilter('request_event_execution', array('eq' => 'after'))
                 as $id => $request) {

            $this->handleCondition($id, $request, $subject);
        }

        return $subject;
    }
}
