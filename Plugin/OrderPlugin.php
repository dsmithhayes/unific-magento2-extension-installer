<?php

namespace Unific\Extension\Plugin;

class OrderPlugin extends AbstractPlugin
{
    protected $entity = 'order';
    protected $subject = 'order/create';

    /**
     * @param $subject
     * @param callable $proceed
     * @param $order
     * @return array
     */
    public function aroundPlace($subject, callable $proceed, $order)
    {
        $this->setSubject($order);
        $this->order = $order;

        if($this->order->getQuoteId() != null)
        {
            $this->quote = $this->quoteFactory->create()->load($this->order->getQuoteId());
        }

        $this->order = $proceed($order);

        if($this->order->getCustomerId() != null)
        {
            $this->customer = $this->customerFactory->create()->load($this->order->getCustomerId());
        }

        foreach ($this->getRequestCollection() as $request)
        {
            $this->handleConditions($request->getId(), $request);
        }

        return $this->order;
    }

    /**
     * @param $subject
     * @param callable $proceed
     * @param $id
     * @return array
     */
    public function aroundCancel($subject, callable $proceed, $id)
    {
        $this->subject = 'order/cancel';
        $this->order = $this->orderRepository->get($id);

        foreach ($this->getRequestCollection('before') as $request)
        {
            $this->handleConditions($request->getId(), $request);
        }

        $result = $proceed($id);

        foreach ($this->getRequestCollection() as $request)
        {
            $this->handleConditions($request->getId(), $request);
        }

        return $result;
    }

    /**
     * @param $order
     */
    protected function setSubject($order)
    {
        if($order->getOriginalIncrementId())
        {
            $this->subject = 'order/update';
        }
    }
}
