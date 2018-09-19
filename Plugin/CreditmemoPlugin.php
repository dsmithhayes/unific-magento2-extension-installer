<?php

namespace Unific\Extension\Plugin;

class CreditmemoPlugin extends AbstractPlugin
{
    protected $entity = 'order';
    protected $subject = 'order/credit';

    /**
     * @param $subject
     * @param \Magento\Sales\Api\Data\CreditmemoInterface $creditmemo
     * @param bool $offlineRequested
     * @return array
     */
    public function beforeRefund($subject, \Magento\Sales\Api\Data\CreditmemoInterface $creditmemo, $offlineRequested = false)
    {
        foreach ($this->getRequestCollection('Magento\Sales\Api\CreditmemoManagementInterface::save', 'before') as $request)
        {
            $this->handleCondition($request->getId(), $request, $creditmemo);
        }

        return [$creditmemo, $offlineRequested];
    }

    /**
     * @param $subject
     * @param \Magento\Sales\Api\Data\CreditmemoInterface $creditmemo
     * @param bool $offlineRequested
     * @return mixed
     */
    public function afterRefund($subject, \Magento\Sales\Api\Data\CreditmemoInterface $creditmemo, $offlineRequested = false)
    {
        foreach ($this->getRequestCollection('Magento\Sales\Api\CreditmemoManagementInterface::save') as $request)
        {
            $this->handleCondition($request->getId(), $request, $creditmemo);
        }

        return $creditmemo;
    }
}
