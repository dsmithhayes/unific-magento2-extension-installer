<?php

namespace Unific\Extension\Plugin;

class InvoicePlugin extends AbstractPlugin
{
    protected $entity = 'invoice';
    protected $subject = 'invoice/create';

    /**
     * @param $invoice
     * @return array
     */
    public function beforeCapture($invoice)
    {
        foreach ($this->getRequestCollection('Magento\Sales\Model\Order\Invoice::capture', 'before') as $request)
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
        foreach ($this->getRequestCollection('Magento\Sales\Model\Order\Invoice::capture') as $request)
        {
            $this->handleCondition($request->getId(), $request, $invoice);
        }

        return $invoice;
    }
}
