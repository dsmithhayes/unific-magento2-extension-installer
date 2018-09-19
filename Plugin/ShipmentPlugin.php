<?php

namespace Unific\Extension\Plugin;

class ShipmentPlugin extends AbstractPlugin
{
    protected $entity = 'shipment';
    protected $subject = 'order/ship';

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
            $order = $this->objectManager->create('Magento\Sales\Model\Order')->load($orderId);
            $this->handleCondition($request->getId(), $request, $order);
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
            $order = $this->objectManager->create('Magento\Sales\Model\Order')->load($orderId);
            $this->handleCondition($request->getId(), $request, $order);
        }

        return [$orderId, $items, $notify, $appendComment, $comment, $tracks, $packages, $arguments];
    }
}
