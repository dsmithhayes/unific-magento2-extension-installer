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
     * ModeManagement constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\Config\ConfigResource\ConfigInterface $configWriter
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface $configWriter
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->configWriter = $configWriter;
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
        $this->configWriter->saveConfig('unific/extension/mode', 'burst', \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);

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
}