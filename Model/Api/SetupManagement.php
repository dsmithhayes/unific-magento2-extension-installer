<?php

namespace Unific\Extension\Model\Api;

use Unific\Extension\Api\SetupManagementInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class SetupManagement implements SetupManagementInterface
{
    /**
     *  @var \Magento\Framework\App\Config\ConfigResource\ConfigInterface
     */
    protected $configInterface;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Unific\Extension\Api\Data\HmacInterface
     */
    protected $hmacInterface;

    /**
     * @var \Unific\Extension\Api\Data\TotalsInterface
     */
    protected $totalsInterface;

    /**
     * @var \Unific\Extension\Api\Data\SetupResponseInterface
     */
    protected $setupResponseInterface;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $customerCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $cacheTypeList;

    /**
     * @var \Magento\Framework\App\Cache\Frontend\Pool
     */
    protected $cacheFrontendPool;

    protected $cacheTypes = array(
        'config',
        'layout',
        'block_html',
        'collections',
        'reflection',
        'db_ddl',
        'eav',
        'config_integration',
        'config_integration_api',
        'full_page',
        'translate',
        'config_webservice'
    );


    /**
     * ModeManagement constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\Config\ConfigResource\ConfigInterface $configInterface
     * @param \Unific\Extension\Api\Data\HmacInterface $hmacInterface
     * @param \Unific\Extension\Api\Data\TotalsInterface $totalsInterface
     * @param \Unific\Extension\Api\Data\SetupResponseInterface $setupResponseInterface
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface $configInterface,
        \Unific\Extension\Api\Data\HmacInterface $hmacInterface,
        \Unific\Extension\Api\Data\TotalsInterface $totalsInterface,
        \Unific\Extension\Api\Data\SetupResponseInterface $setupResponseInterface,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->configInterface = $configInterface;
        $this->hmacInterface = $hmacInterface;
        $this->totalsInterface = $totalsInterface;
        $this->setupResponseInterface = $setupResponseInterface;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->cacheTypeList = $cacheTypeList;
        $this->cacheFrontendPool = $cacheFrontendPool;
    }

    /**
     * Gets the hmac security data
     *
     * @api
     *
     * @return \Unific\Extension\Api\Data\SetupResponseInterface
     */
    public function getData(\Unific\Extension\Api\Data\IntegrationInterface $integration)
    {
        $this->configInterface->saveConfig('unific/extension/integration', $integration->getIntegrationId(), ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);

        // Clear the caches to respect the config changes
        $this->clearCache();

        $this->hmacInterface->setHmacHeader($this->scopeConfig->getValue('unific/hmac/hmacHeader', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
        $this->hmacInterface->setHmacSecret($this->scopeConfig->getValue('unific/hmac/hmacSecret', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
        $this->hmacInterface->setHmacAlgorithm($this->scopeConfig->getValue('unific/hmac/hmacAlgorithm', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));

        $this->totalsInterface->setCategory($this->categoryCollectionFactory->create()->getSize());
        $this->totalsInterface->setProduct($this->productCollectionFactory->create()->getSize());
        $this->totalsInterface->setOrder($this->orderCollectionFactory->create()->getSize());
        $this->totalsInterface->setCustomer($this->customerCollectionFactory->create()->getSize());

        $this->setupResponseInterface->setTotals($this->totalsInterface);
        $this->setupResponseInterface->setHmac($this->hmacInterface);

        return $this->setupResponseInterface;
    }

    /**
     * Clear the cache
     */
    protected function clearCache()
    {
        foreach ($this->cacheTypes as $type) {
            $this->cacheTypeList->cleanType($type);
        }
        foreach ($this->cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }
    }
}