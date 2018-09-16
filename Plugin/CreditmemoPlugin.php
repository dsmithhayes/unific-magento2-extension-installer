<?php

namespace Unific\Extension\Plugin;

class CreditmemoPlugin extends AbstractPlugin
{
    protected $entity = 'order';
    protected $subject = 'order/credit';

    /**
     * @param $subject
     * @param $order
     * @return array
     */
    public function beforeSave($subject, $order)
    {
        foreach ($this->getRequestCollection('Magento\Sales\Api\CreditmemoManagementInterface::save', 'before') as $request)
        {
            $this->handleCondition($request->getId(), $request, $order);
        }

        return [$order];
    }

    /**
     * @param $subject
     * @param $order
     * @return mixed
     */
    public function afterSave($subject, $order)
    {
        foreach ($this->getRequestCollection('Magento\Sales\Api\CreditmemoManagementInterface::save') as $request)
        {
            $this->handleCondition($request->getId(), $request, $order);
        }

        return $order;
    }
}
