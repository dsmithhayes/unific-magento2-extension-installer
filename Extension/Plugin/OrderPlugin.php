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
     * @param \Magento\Sales\Api\Data\OrderExtensionFactory|null $orderExtensionFactory
     */
    public function __construct(
        \Unific\Extension\Model\ResourceModel\Metadata $metadata,
        \Unific\Extension\Logger\Logger $logger,
        \Unific\Extension\Helper\Mapping $mapping,
        \Unific\Extension\Connection\Rest\Connection $restConnection,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Api\Data\OrderExtensionFactory $orderExtensionFactory = null
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
            $this->handleCondition($request->getId(), $request,  $this->getFullOrder($order));
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
            $this->handleCondition($request->getId(), $request,  $this->getFullOrder($order));
        }

        return $order;
    }

    /**
     * @param $id
     */
    protected function getFullOrder($order)
    {
        $fullOrder = $this->metadata->getNewInstance()->load($order->getId());

        /** @var OrderExtensionInterface $extensionAttributes */
        $extensionAttributes = $fullOrder->getExtensionAttributes();

        if ($extensionAttributes === null) {
            $extensionAttributes = $this->orderExtensionFactory->create();
        } elseif ($extensionAttributes->getShippingAssignments() !== null) {
        }

        /** @var ShippingAssignmentInterface $shippingAssignment */
        $shippingAssignments = \Magento\Framework\App\ObjectManager::getInstance()->get(
            \Magento\Sales\Model\Order\ShippingAssignmentBuilder::class
        );

        $shippingAssignments->setOrderId($fullOrder->getEntityId());
        $extensionAttributes->setShippingAssignments($shippingAssignments->create());
        $fullOrder->setExtensionAttributes($extensionAttributes);

        return $fullOrder;
    }
}
