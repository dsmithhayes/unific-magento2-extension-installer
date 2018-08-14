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
        foreach ($this->getRequestCollection()
                     ->addFieldToFilter('request_event', array('eq' => 'Magento\Sales\Model\Order\Invoice::capture'))
                     ->addFieldToFilter('request_event_execution', array('eq' => 'before'))
                 as $id => $request) {

            $this->handleCondition($id, $request, $invoice);
        }

        return [$invoice];
    }

    /**
     * @param $invoice
     * @return mixed
     */
    public function afterCapture($invoice)
    {
        foreach ($this->getRequestCollection()
                     ->addFieldToFilter('request_event', array('eq' => 'Magento\Sales\Model\Order\Invoice::capture'))
                     ->addFieldToFilter('request_event_execution', array('eq' => 'after'))
                 as $id => $request) {

            $this->handleCondition($id, $request, $invoice);
        }

        return $invoice;
    }
}
