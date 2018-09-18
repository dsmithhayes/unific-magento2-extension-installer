<?php

namespace Unific\Extension\Plugin;

use Unific\Extension\Model\ResourceModel\Metadata;

class OrderPlugin extends AbstractPlugin
{
    protected $entity = 'order';
    protected $subject = 'order/create';

    /**
     * OrderPlugin constructor.
     * @param \Unific\Extension\Logger\Logger $logger
     * @param \Unific\Extension\Helper\Mapping $mapping
     * @param \Unific\Extension\Connection\Rest\Connection $restConnection
     */
    public function __construct(
        \Unific\Extension\Logger\Logger $logger,
        \Unific\Extension\Helper\Mapping $mapping,
        \Unific\Extension\Connection\Rest\Connection $restConnection
    )
    {
        $this->logger = $logger;
        $this->mappingHelper = $mapping;
        $this->restConnection = $restConnection;

        parent::__construct($logger, $mapping, $restConnection);
    }

    /**
     * @param $subject
     * @param $order
     * @return array
     */
    public function beforePlace($subject, $order)
    {
        foreach ($this->getRequestCollection('Magento\Sales\Api\OrderManagementInterface::place', 'before') as $request)
        {
            $this->setSubject($order);
            $this->handleCondition($request->getId(), $request,  $this->getOrderInfo($order));
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
            $this->setSubject($order);
            $this->handleCondition($request->getId(), $request,  $this->getOrderInfo($order));
        }

        return $order;
    }

    /**
     * @param $order
     * @return mixed
     */
    protected function getOrderInfo($order)
    {
        $returnData = $order->getData();

        $returnData['items'] = array();
        foreach($order->getAllItems() as $item)
        {
            $returnData['items'][] = $item->getData();
        }

        $returnData['addresses'] = array();
        $returnData['addresses']['billing'] = $order->getBillingAddress()->getData();
        $returnData['addresses']['shipping'] = $order->getShippingAddress()->getData();
        $returnData['payment'] = $order->getPayment()->getData();

        return $returnData;
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
