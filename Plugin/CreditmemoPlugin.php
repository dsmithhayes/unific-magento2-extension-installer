<?php

namespace Unific\Extension\Plugin;

class CreditmemoPlugin extends AbstractPlugin
{
    protected $entity = 'order';
    protected $subject = 'order/refund';

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
     * @param bool $capture
     * @param array $items
     * @param bool $notify
     * @param bool $appendComment
     * @param \Magento\Sales\Api\Data\InvoiceCommentCreationInterface $comment
     * @param \Magento\Sales\Api\Data\InvoiceCreationArgumentsInterface $arguments
     * @return array
     */
    public function beforeExecute($subject,
                                  $orderId,
                                  $capture = false,
                                  array $items = [],
                                  $notify = false,
                                  $appendComment = false,
                                  \Magento\Sales\Api\Data\InvoiceCommentCreationInterface $comment = null,
                                  \Magento\Sales\Api\Data\InvoiceCreationArgumentsInterface $arguments = null)
    {
        foreach ($this->getRequestCollection($this->subject, 'before') as $request)
        {
            $this->handleCondition($request->getId(), $request,  $this->orderRepository->get($orderId));
        }

        return [$orderId, $capture, $items, $notify, $appendComment, $comment, $arguments];
    }

    /**
     * @param $subject
     * @param $orderId
     * @param bool $capture
     * @param array $items
     * @param bool $notify
     * @param bool $appendComment
     * @param \Magento\Sales\Api\Data\InvoiceCommentCreationInterface $comment
     * @param \Magento\Sales\Api\Data\InvoiceCreationArgumentsInterface $arguments
     * @return mixed
     */
    public function afterExecute($subject,
                                 $orderId,
                                 $capture = false,
                                 array $items = [],
                                 $notify = false,
                                 $appendComment = false,
                                 \Magento\Sales\Api\Data\InvoiceCommentCreationInterface $comment = null,
                                 \Magento\Sales\Api\Data\InvoiceCreationArgumentsInterface $arguments = null)
    {
        foreach ($this->getRequestCollection($this->subject) as $request)
        {
            $this->handleCondition($request->getId(), $request,  $this->orderRepository->get($orderId));
        }

        return [$orderId, $capture, $items, $notify, $appendComment, $comment, $arguments];
    }
}
