<?php

namespace Unific\Extension\Plugin;

class InvoicePlugin extends AbstractPlugin
{
    protected $entity = 'invoice';
    protected $subject = 'order/invoice';

    /**
     * @param $invoice
     * @return array
     */
    public function beforeCapture($invoice)
    {
        foreach ($this->getRequestCollection($this->subject, 'before') as $request)
        {
            $this->handleCondition($request->getId(), $request, $invoice);
        }

        return [$invoice];
    }

    /**
     * @param $invoice
     * @return mixed
     */
    public function afterCapture($invoice)
    {
        foreach ($this->getRequestCollection($this->subject) as $request)
        {
            $this->handleCondition($request->getId(), $request, $invoice);
        }

        return $invoice;
    }
}
