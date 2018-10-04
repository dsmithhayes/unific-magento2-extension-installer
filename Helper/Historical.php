<?php

namespace Unific\Extension\Helper;

use Magento\Framework\Exception\InputException;
use Symfony\Component\Config\Definition\Exception\Exception;

class Historical extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $logger;
    protected $restConnection;
    protected $queueCollection;
    protected $scopeConfig;

    protected $entitites = array(
        'order',
        'shipment',
        'creditnota',
        'invoice',
        'customer',
        'category',
        'product'
    );

    /**
     * Request constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Unific\Extension\Model\Message\Queue $queueCollection
     * @param \Unific\Extension\Logger\Logger $logger
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Unific\Extension\Model\Message\Queue $queueCollection,
        \Unific\Extension\Logger\Logger $logger,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory)
    {
        parent::__construct($context);

        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
        $this->queueCollection = $queueCollection;
    }

    public function queueAllHistoricalData()
    {
        // Fetch all entities
        // Write to message queue
    }
}
