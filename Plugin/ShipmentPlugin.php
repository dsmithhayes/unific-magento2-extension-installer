<?php

namespace Unific\Extension\Plugin;

class ShipmentPlugin extends AbstractPlugin
{
    protected $entity = 'shipment';
    protected $subject = 'order/ship';

    protected $orderRepository;

    /**
     * OrderPlugin constructor.
     * @param \Unific\Extension\Logger\Logger $logger
     * @param \Unific\Extension\Helper\Mapping $mapping
     * @param \Unific\Extension\Connection\Rest\Connection $restConnection
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        \Unific\Extension\Logger\Logger $logger,
        \Unific\Extension\Helper\Mapping $mapping,
        \Unific\Extension\Connection\Rest\Connection $restConnection,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    )
    {
        $this->logger = $logger;
        $this->mappingHelper = $mapping;
        $this->restConnection = $restConnection;
        $this->orderRepository = $orderRepository;

        parent::__construct($logger, $mapping, $restConnection);
    }

    /**
     * @param $subject
     * @param $orderId
     * @param array $items
     * @param bool $notify
     * @param bool $appendComment
     * @param \Magento\Sales\Api\Data\ShipmentCommentCreationInterface|null $comment
     * @param array $tracks
     * @param array $packages
     * @param \Magento\Sales\Api\Data\ShipmentCreationArgumentsInterface|null $arguments
     * @return array
     */
    public function beforeExecute($subject,
                                  $orderId,
                                  array $items = [],
                                  $notify = false,
                                  $appendComment = false,
                                  \Magento\Sales\Api\Data\ShipmentCommentCreationInterface $comment = null,
                                  array $tracks = [],
                                  array $packages = [],
                                  \Magento\Sales\Api\Data\ShipmentCreationArgumentsInterface $arguments = null
    )
    {
        foreach ($this->getRequestCollection($this->subject, 'before') as $request)
        {
            $this->handleCondition($request->getId(), $request,  $this->orderRepository->get($orderId));
        }

        return [$orderId, $items, $notify, $appendComment, $comment, $tracks, $packages, $arguments];
    }

    /**
     * @param $subject
     * @param $orderId
     * @param array $items
     * @param bool $notify
     * @param bool $appendComment
     * @param \Magento\Sales\Api\Data\ShipmentCommentCreationInterface|null $comment
     * @param array $tracks
     * @param array $packages
     * @param \Magento\Sales\Api\Data\ShipmentCreationArgumentsInterface|null $arguments
     * @return mixed
     */
    public function afterExecute($subject,
                                 $orderId,
                                 array $items = [],
                                 $notify = false,
                                 $appendComment = false,
                                 \Magento\Sales\Api\Data\ShipmentCommentCreationInterface $comment = null,
                                 array $tracks = [],
                                 array $packages = [],
                                 \Magento\Sales\Api\Data\ShipmentCreationArgumentsInterface $arguments = null)
    {
        foreach ($this->getRequestCollection($this->subject) as $request)
        {
            $this->handleCondition($request->getId(), $request,  $this->orderRepository->get($orderId));
        }

        return [$orderId, $items, $notify, $appendComment, $comment, $tracks, $packages, $arguments];
    }
}
