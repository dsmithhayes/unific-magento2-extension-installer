<?php

namespace Unific\Extension\Model\Api;

use Unific\Extension\Api\ModeManagementInterface;

class ModeManagement implements ModeManagementInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    protected $configWriter;

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
    );

    /**
     * ModeManagement constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\Config\ConfigResource\ConfigInterface $configWriter
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface $configWriter,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->configWriter = $configWriter;
        $this->cacheTypeList = $cacheTypeList;
        $this->cacheFrontendPool = $cacheFrontendPool;
    }

    /**
     * Sets the mode
     *
     * @api
     * @param string $mode burst or live
     *
     * @return bool true on success
     */
    public function setMode($mode)
    {
        $this->configWriter->saveConfig('unific/extension/mode', $mode, \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->clearCache();

        return true;
    }

    /**
     * Gets the current mode
     *
     * @api
     *
     * @return string
     */
    public function getMode()
    {
        return $this->scopeConfig->getValue('unific/extension/mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
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