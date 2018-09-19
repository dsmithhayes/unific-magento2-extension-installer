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
     * @param \Magento\Sales\Api\Data\CreditmemoInterface $creditmemo
     * @param bool $offlineRequested
     * @return array
     */
    public function beforeRefund($subject,
                                 \Magento\Sales\Api\Data\CreditmemoInterface $creditmemo,
                                 $offlineRequested = false)
    {
        foreach ($this->getRequestCollection($this->subject, 'before') as $request)
        {
            $this->handleCondition($request->getId(), $request,  $this->orderRepository->get($creditmemo->getOrderId()));
        }

        return [$creditmemo, $offlineRequested];
    }

    /**
     * @param $subject
     * @param \Magento\Sales\Api\Data\CreditmemoInterface $creditmemo
     * @param bool $offlineRequested
     * @return mixed
     */
    public function afterExecute($subject,
                                 \Magento\Sales\Api\Data\CreditmemoInterface $creditmemo,
                                 $offlineRequested = false)
    {
        foreach ($this->getRequestCollection($this->subject) as $request)
        {
            $this->handleCondition($request->getId(), $request,  $this->orderRepository->get($creditmemo->getOrderId()));
        }

        return [$creditmemo, $offlineRequested];
    }
}
