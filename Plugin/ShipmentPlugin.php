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
     * @return void
     */
    public function beforeRegister($subject)
    {
        foreach ($this->getRequestCollection($this->subject, 'before') as $request)
        {
            $this->handleCondition($request->getId(), $request,  $subject->getOrder());
        }

        return [$subject];
    }

    /**
     * @param $subject
     * @return mixed
     */
    public function afterRegister($subject)
    {
        foreach ($this->getRequestCollection($this->subject) as $request)
        {
            $this->handleCondition($request->getId(), $request,  $subject->getOrder());
        }

        return $subject;
    }
}
