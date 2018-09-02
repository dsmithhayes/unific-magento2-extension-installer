<?php

namespace Unific\Extension\Plugin;

class OrderPlugin extends AbstractPlugin
{
    protected $entity = 'order';
    protected $subject = 'order/create';

    /**
     * @var Metadata
     */
    protected $metadata;

    /**
     * @var SearchResultFactory
     */
    protected $searchResultFactory = null;

    /**
     * @var OrderExtensionFactory
     */
    private $orderExtensionFactory;

    /**
     * @var ShippingAssignmentBuilder
     */
    private $shippingAssignmentBuilder;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var OrderInterface[]
     */
    protected $registry = [];


    /**
     * OrderPlugin constructor.
     * @param \Unific\Extension\Logger\Logger $logger
     * @param \Unific\Extension\Helper\Mapping $mapping
     * @param \Unific\Extension\Connection\Rest\Connection $restConnection
     * @param \Magento\Sales\Model\ResourceModel\Metadata $metadata
     * @param \Magento\Sales\Api\Data\OrderSearchResultInterfaceFactory $searchResultFactory
     * @param \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface|null $collectionProcessor
     * @param \Magento\Sales\Api\Data\OrderExtensionFactory|null $orderExtensionFactory
     */
    public function __construct(
        \Unific\Extension\Logger\Logger $logger,
        \Unific\Extension\Helper\Mapping $mapping,
        \Unific\Extension\Connection\Rest\Connection $restConnection,
        \Magento\Sales\Model\ResourceModel\Metadata $metadata,
        \Magento\Sales\Api\Data\OrderSearchResultInterfaceFactory $searchResultFactory,
        \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor = null,
        \Magento\Sales\Api\Data\OrderExtensionFactory $orderExtensionFactory = null
    )
    {
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $this->logger = $logger;
        $this->mappingHelper = $mapping;
        $this->restConnection = $restConnection;

        $this->metadata = $metadata;
        $this->searchResultFactory = $searchResultFactory;
        $this->collectionProcessor = $collectionProcessor ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface::class);
        $this->orderExtensionFactory = $orderExtensionFactory ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Sales\Api\Data\OrderExtensionFactory::class);

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
            $this->handleCondition($request->getId(), $request, $this->metadata->getNewInstance()->load($order->getId()));
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
            $this->handleCondition($request->getId(), $request, $this->metadata->getNewInstance()->load($order->getId());
        }

        return $order;
    }
}
