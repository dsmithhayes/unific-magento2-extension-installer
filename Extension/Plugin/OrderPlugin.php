<?php

namespace Unific\Extension\Plugin;

class OrderPlugin extends AbstractPlugin
{
    protected $entity = 'order';
    protected $subject = 'order/create';

    /**
     * @param $subject
     * @param $order
     * @return array
     */
    public function beforePlace($subject, $order)
    {
        foreach ($this->getRequestCollection('Magento\Sales\Api\OrderManagementInterface::place', 'before') as $request)
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
    public function afterPlace($subject, $order)
    {
        foreach ($this->getRequestCollection('Magento\Sales\Api\OrderManagementInterface::place') as $request)
        {
            $this->handleCondition($request->getId(), $request, $order);
        }

        return $order;
    }
}
