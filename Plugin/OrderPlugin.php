<?php

namespace Unific\Extension\Plugin;

use Unific\Extension\Model\ResourceModel\Metadata;

class OrderPlugin extends AbstractPlugin
{
    protected $entity = 'order';
    protected $subject = 'order/create';

    protected $orderRepository;
    protected $searchCriteriaBuilder;

    protected $metadata;
    protected $orderExtensionFactory;

    /**
     * OrderPlugin constructor.
     * @param Metadata $metadata
     * @param \Unific\Extension\Logger\Logger $logger
     * @param \Unific\Extension\Helper\Mapping $mapping
     * @param \Unific\Extension\Connection\Rest\Connection $restConnection
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Sales\Api\Data\OrderExtensionFactory $orderExtensionFactory
     */
    public function __construct(
        \Unific\Extension\Model\ResourceModel\Metadata $metadata,
        \Unific\Extension\Logger\Logger $logger,
        \Unific\Extension\Helper\Mapping $mapping,
        \Unific\Extension\Connection\Rest\Connection $restConnection,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Api\Data\OrderExtensionFactory $orderExtensionFactory
    )
    {
        $this->logger = $logger;
        $this->mappingHelper = $mapping;
        $this->restConnection = $restConnection;

        $this->orderRepository = $orderRepository;
        $this->metadata = $metadata;
        $this->orderExtensionFactory = $orderExtensionFactory;

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
