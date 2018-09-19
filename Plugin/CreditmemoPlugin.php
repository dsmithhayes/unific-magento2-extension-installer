<?php

namespace Unific\Extension\Plugin;

class CreditmemoPlugin extends AbstractPlugin
{
    protected $entity = 'order';
    protected $subject = 'order/refund';

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
            $order = $this->orderRepository->get($subject->getOrder()->getId());
            $this->handleCondition($request->getId(), $request,  $this->getOrderInfo($order));
        }

        return [$creditmemo, $offlineRequested];
    }

    /**
     * @param $subject
     * @param \Magento\Sales\Api\Data\CreditmemoInterface $creditmemo
     * @param bool $offlineRequested
     * @return mixed
     */
    public function afterRefund($subject,
                                 \Magento\Sales\Api\Data\CreditmemoInterface $creditmemo,
                                 $offlineRequested = false)
    {
        foreach ($this->getRequestCollection($this->subject) as $request)
        {
            $order = $this->orderRepository->get($subject->getOrder()->getId());
            $this->handleCondition($request->getId(), $request,  $this->getOrderInfo($order));
        }

        return [$creditmemo, $offlineRequested];
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
