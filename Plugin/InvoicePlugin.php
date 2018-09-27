<?php

namespace Unific\Extension\Plugin;

class InvoicePlugin extends AbstractPlugin
{
    protected $entity = 'order';
    protected $subject = 'order/invoice';

    /**
     * @param $subject
     * @param callable $proceed
     * @return void
     */
    public function aroundRegister($subject, callable $proceed)
    {
        $this->order = $this->orderRepository->get($subject->getOrder()->getId());

        foreach ($this->getRequestCollection('before') as $request)
        {
            $this->handleConditions($request->getId(), $request);
        }

        $result = $proceed();

        foreach ($this->getRequestCollection() as $request)
        {
            $this->handleConditions($request->getId(), $request);
        }

        return $result;
    }
}
