<?php

namespace Unific\Extension\Plugin;

class CreditmemoPlugin extends AbstractPlugin
{
    protected $entity = 'order';
    protected $subject = 'order/refund';

    /**
     * @param $subject
     * @param callable $proceed
     * @param \Magento\Sales\Api\Data\CreditmemoInterface $creditmemo
     * @param bool $offlineRequested
     * @return array
     */
    public function aroundRefund($subject,
                                 callable $proceed,
                                 \Magento\Sales\Api\Data\CreditmemoInterface $creditmemo,
                                 $offlineRequested = false)
    {
        $this->order = $this->orderRepository->get($creditmemo->getOrderId());

        foreach ($this->getRequestCollection('before') as $request)
        {
            $this->handleConditions($request->getId(), $request);
        }

        $result = $proceed($creditmemo, $offlineRequested);

        foreach ($this->getRequestCollection() as $request)
        {
            $this->handleConditions($request->getId(), $request);
        }

        return $result;
    }
}
