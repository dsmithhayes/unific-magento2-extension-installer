<?php

namespace Unific\Extension\Plugin;

use Unific\Extension\Model\ResourceModel\Metadata;

class OrderUpdatePlugin extends AbstractPlugin
{
    protected $entity = 'order';
    protected $subject = 'order/update';

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
    public function beforeSave($subject, $order)
    {
        foreach ($this->getRequestCollection('Magento\Sales\Api\Order::save', 'before') as $request)
        {
            $this->handleCondition($request->getId(), $request,  $order);
        }

        return [$order];
    }

    /**
     * @param $subject
     * @param $order
     * @return mixed
     */
    public function afterSave($subject, $order)
    {
        foreach ($this->getRequestCollection('Magento\Sales\Api\Order::save') as $request)
        {
            $this->handleCondition($request->getId(), $request,  $order);
        }

        return $order;
    }

    /**
     * @param $id
     */
    protected function setExtensionData($order)
    {
        /** @var OrderExtensionInterface $extensionAttributes */
        $extensionAttributes = $order->getExtensionAttributes();

        if ($extensionAttributes === null) {
            $extensionAttributes = $this->orderExtensionFactory->create();
        } elseif ($extensionAttributes->getShippingAssignments() !== null) {
            return;
        }

        /** @var ShippingAssignmentInterface $shippingAssignment */
        $shippingAssignments = \Magento\Framework\App\ObjectManager::getInstance()->get(
            \Magento\Sales\Model\Order\ShippingAssignmentBuilder::class
        );

        $shippingAssignments->setOrderId($order->getEntityId());
        $extensionAttributes->setShippingAssignments($shippingAssignments->create());
        $order->setExtensionAttributes($extensionAttributes);
    }
}
