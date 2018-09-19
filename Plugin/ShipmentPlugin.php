<?php

namespace Unific\Extension\Plugin;

class ShipmentPlugin extends AbstractPlugin
{
    protected $entity = 'shipment';
    protected $subject = 'order/ship';

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
            $order = $this->orderRepository->get($subject->getOrder()->getId());
            $this->handleCondition($request->getId(), $request,  $this->getOrderInfo($order));
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
            $order = $this->orderRepository->get($subject->getOrder()->getId());
            $this->handleCondition($request->getId(), $request,  $this->getOrderInfo($order));
        }
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
}
