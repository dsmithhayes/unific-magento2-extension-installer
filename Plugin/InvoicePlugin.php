<?php

namespace Unific\Extension\Plugin;

class InvoicePlugin extends AbstractPlugin
{
    protected $entity = 'invoice';
    protected $subject = 'order/invoice';

    protected $orderRepository;

    /**
     * @param \Unific\Extension\Logger\Logger $logger
     * @param \Unific\Extension\Helper\Mapping $mapping
     * @param \Unific\Extension\Connection\Rest\Connection $restConnection
     * @param \Unific\Extension\Model\ResourceModel\Request\Grid\CollectionFactory $collectionFactory
     * @param \Unific\Extension\Model\RequestFactory $requestFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        \Unific\Extension\Logger\Logger $logger,
        \Unific\Extension\Helper\Mapping $mapping,
        \Unific\Extension\Connection\Rest\Connection $restConnection,
        \Unific\Extension\Model\ResourceModel\Request\Grid\CollectionFactory $collectionFactory,
        \Unific\Extension\Model\RequestFactory $requestFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    )
    {
        $this->orderRepository = $orderRepository;

        parent::__construct($logger, $mapping, $restConnection, $collectionFactory, $requestFactory);
    }

    /**
     * @param $subject
     * @return void
     */
    public function beforeRegister($subject)
    {
        foreach ($this->getRequestCollection($this->subject, 'before') as $request)
        {
            $this->handleCondition($request->getId(), $request,  $this->orderRepository->get($subject->getOrder()->getId()));
        }
    }

    /**
     * @param $subject
     * @return mixed
     */
    public function afterRegister($subject)
    {
        foreach ($this->getRequestCollection($this->subject) as $request)
        {
            $this->handleCondition($request->getId(), $request,  $this->orderRepository->get($subject->getOrder()->getId()));
        }
    }
}
