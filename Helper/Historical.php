<?php

namespace Unific\Extension\Helper;

use Magento\Framework\Exception\InputException;
use Symfony\Component\Config\Definition\Exception\Exception;

class Historical extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $hmacKey = '';

    protected $subject = 'historical/order';

    protected $logger;
    protected $queueHelper;
    protected $hmacHelper;
    protected $scopeConfig;

    protected $searchCriteriaBuilder;

    protected $orderRepository;
    protected $customerRepository;

    protected $categoryRepository;
    protected $productRepository;

    protected $writeBuffer = array();

    /**
     * ModeManagement constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param Message\Queue $queueHelper
     * @param Hmac $hmacHelper
     * @param \Unific\Extension\Logger\Logger $logger
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param \Magento\Customer\Model\ResourceModel\CustomerRepository $customerRepository
     * @param \Magento\Catalog\Model\CategoryRepository $categoryRepository
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Unific\Extension\Helper\Message\Queue $queueHelper,
        \Unific\Extension\Helper\Hmac $hmacHelper,
        \Unific\Extension\Logger\Logger $logger,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Magento\Customer\Model\ResourceModel\CustomerRepository $customerRepository,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Catalog\Model\ProductRepository $productRepository
    )
    {
        parent::__construct($context);

        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
        $this->queueHelper = $queueHelper;
        $this->hmacHelper = $hmacHelper;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;

    }

    /**
     * Queue up all the historical data to be ready for sending
     */
    public function queueAllHistoricalData()
    {
        $this->hmacKey = $this->scopeConfig->getValue('unific/hmac/hmacHeader', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        // Queue Categories
        $this->subject = 'historical/categories';
        foreach ($this->categoryRepository->getList($this->searchCriteriaBuilder->create()) as $category) {
            $this->writeBuffer[] = $category->getData();
            $this->processWriteBuffer();
        }

        // Force a flush to not get mixed subjects in one historical buffered queue
        $this->processWriteBuffer(true);

        // Queue Products
        $this->subject = 'historical/products';
        foreach ($this->productRepository->getList($this->searchCriteriaBuilder->create()) as $product) {
            $this->writeBuffer[] = $product->getData();
            $this->processWriteBuffer();
        }

        // Force a flush to not get mixed subjects in one historical buffered queue
        $this->processWriteBuffer(true);

        // Queue Customers
        $this->subject = 'historical/customers';
        foreach ($this->customerRepository->getList($this->searchCriteriaBuilder->create()) as $customer) {
            $this->writeBuffer[] = $customer->getData();
            $this->processWriteBuffer();
        }

        // Force a flush to not get mixed subjects in one historical buffered queue
        $this->processWriteBuffer(true);

        // Queue Orders
        $this->subject = 'historical/orders';
        foreach ($this->orderRepository->getList($this->searchCriteriaBuilder->create()) as $order) {
            $this->writeBuffer[] = $order->getData();
            $this->processWriteBuffer();
        }

        // Force a flush to not get mixed subjects in one historical buffered queue
        $this->processWriteBuffer(true);
    }

    /**
     * Flush the buffer if its full or forced
     * A buffer will never be sent if its empty
     *
     * @param bool $forceFlush
     */
    public function processWriteBuffer($forceFlush = false)
    {
        // If we have data and its either a full buffer or we forced a flush
        if (count($this->writeBuffer > 0) && (($this->writeBuffer) >= 10 || $forceFlush)) {
            $extraHeaders = array();
            if ($this->scopeConfig->getValue('unific/hmac/hmacEnable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
                $extraHeaders[$this->hmacKey] = $this->hmacHelper->generateHmac($this->writeBuffer);
            }

            $this->queueHelper->queue($this->scopeConfig->getValue('unific/extension/endpoint'), $this->writeBuffer, $extraHeaders, \Zend_Http_Client::POST, true);

            // Truncate the write buffer
            $this->writeBuffer = array();
        }
    }
}
