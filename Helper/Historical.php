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

    protected $categoryFactory;
    protected $productFactory;

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
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productFactory
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
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productFactory
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
        $this->productFactory = $productFactory;
        $this->categoryFactory = $categoryFactory;

    }

    /**
     * Queue up all the historical data to be ready for sending
     */
    public function queueAllHistoricalData()
    {
        $this->hmacKey = $this->scopeConfig->getValue('unific/hmac/hmacHeader', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        // Queue Orders
        $this->subject = 'historical/orders';
        foreach ($this->orderRepository->getList($this->searchCriteriaBuilder->create()) as $order) {
            $this->writeBuffer[] = $this->getOrderInfo($order);
            $this->processWriteBuffer();
        }

        // Force a flush to not get mixed subjects in one historical buffered queue
        $this->processWriteBuffer(true);

        // Queue Customers
        $this->subject = 'historical/customers';
        foreach ($this->customerRepository->getList($this->searchCriteriaBuilder->create())->getItems() as $customer) {
            $this->writeBuffer[] = $this->getCustomerInfo($customer);
            $this->processWriteBuffer();
        }

        // Force a flush to not get mixed subjects in one historical buffered queue
        $this->processWriteBuffer(true);

        // Queue Categories
        $this->subject = 'historical/categories';
        $categories = $this->categoryFactory->create()->addAttributeToSelect('*');
        foreach ($categories as $category) {
            $this->writeBuffer[] = $category->getData();
            $this->processWriteBuffer();
        }

        // Force a flush to not get mixed subjects in one historical buffered queue
        $this->processWriteBuffer(true);

        // Queue Products
        $this->subject = 'historical/products';
        $products = $this->productFactory->create()->addAttributeToSelect('*');
        foreach ($products as $product) {
            $this->writeBuffer[] = $product->getData();
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
        if (count($this->writeBuffer > 0) && (count($this->writeBuffer) >= 10 || $forceFlush)) {
            $extraHeaders = array();

            $extraHeaders['X-SUBJECT'] = $this->subject;

            if ($this->scopeConfig->getValue('unific/hmac/hmacEnable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
                $extraHeaders[$this->hmacKey] = $this->hmacHelper->generateHmac($this->writeBuffer);
            }

            $this->queueHelper->queue($this->scopeConfig->getValue('unific/extension/endpoint'), $this->writeBuffer, $extraHeaders, \Zend_Http_Client::POST, true);

            // Truncate the write buffer
            $this->writeBuffer = array();
        }
    }

    /**
     * @return mixed
     */
    protected function getCustomerInfo($customer)
    {
        $returnData = array();

        try {
            $returnData['email'] = $customer->getEmail();
            $returnData['prefix'] = $customer->getPrefix();
            $returnData['firstname'] = $customer->getFirstname();
            $returnData['middlename'] = $customer->getMiddlename();
            $returnData['lastname'] = $customer->getLastname();
            $returnData['suffix'] = $customer->getSuffix();
            $returnData['dob'] = $customer->getDob();
            $returnData['gender'] = $customer->getGender();

            $returnData['created_at'] = $customer->getCreatedAt();
            $returnData['updated_at'] = $customer->getUpdatedAt();

            if($returnData['created_at'] == null)
            {
                $returnData['created_at'] = date('Y-m-d H:i:s');
            }

            if($returnData['updated_at'] == null)
            {
                $returnData['updated_at'] = date('Y-m-d H:i:s');
            }

            $returnData['addresses'] = array();
            $addresses = $customer->getAddresses();

            foreach($addresses as $address)
            {
                $returnData['addresses'][] = array(
                    'firstname' => $address->getFirstname(),
                    'middlename' => $address->getMiddlename(),
                    'lastname' => $address->getLastname(),
                    'street' => (is_string($address->getStreet()) ? explode('\n', $address->getStreet()) : $address->getStreet()),
                    'postcode' => $address->getPostcode(),
                    'city' => $address->getCompany(),
                    'country' => $address->getCountryId(),
                    'telephone' => $address->getTelephone(),
                    'company' => $address->getCompany()
                );
            }
        } catch(\Exception $e)
        {
            $this->logger->error('Could not set customer info: ' . $e->getMessage());
        }

        return $returnData;
    }

    /**
     * @return mixed
     */
    protected function getOrderInfo($order)
    {
        $returnData = array();

        try {
            $returnData = $order->getData();
            $returnData['customer_is_guest'] = (isset($returnData['customer_is_guest'])) ? (int) $returnData['customer_is_guest'] : 0;

            $returnData['order_items'] = array();
            foreach($order->getAllItems() as $item)
            {
                $itemData = $item->getData();
                $itemData['free_shipping'] = (isset($itemData['free_shipping']) && $itemData['free_shipping'] == true) ? 1 : 0;

                if(isset($itemData['is_qty_decimal']))
                {
                    $itemData['is_qty_decimal'] = (int)$itemData['is_qty_decimal'];
                }

                if(isset($itemData['qty_ordered']))
                {
                    $itemData['qty_ordered'] = (int)$itemData['qty_ordered'];
                }

                if(isset($itemData['qty_canceled']))
                {
                    $itemData['qty_canceled'] = (int)$itemData['qty_canceled'];
                }

                if(isset($itemData['qty_invoiced']))
                {
                    $itemData['qty_invoiced'] = (int)$itemData['qty_invoiced'];
                }

                if(isset($itemData['qty_refunded']))
                {
                    $itemData['qty_refunded'] = (int)$itemData['qty_refunded'];
                }

                if(isset($itemData['qty_shipped']))
                {
                    $itemData['qty_shipped'] = (int)$itemData['qty_shipped'];

                }

                $returnData['order_items'][] = $itemData;
            }

            $returnData['addresses'] = array();
            $returnData['addresses']['billing'] = $order->getBillingAddress()->getData();
            $returnData['addresses']['shipping'] = $order->getShippingAddress()->getData();
            $returnData['payment'] = $order->getPayment()->getData();
        } catch (\Exception $e)
        {
            $this->logger->info('Could not set order info: ' . $e->getMessage());
        }

        return $returnData;
    }
}
