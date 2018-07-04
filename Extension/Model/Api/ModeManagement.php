<?php

namespace Unific\Extension\Model\Api;

use Unific\Extension\Api\ModeManagementInterface;

class ModeManagement implements ModeManagementInterface
{
    protected $scopeConfig;

    /**
     * ModeManagement constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Sets the mode
     *
     * @api
     * @param string $mode burst or live
     * @param int $intervalInSeconds when in burst mode
     *
     * @return bool true on success
     */
    public function setMode($mode, $intervalInSeconds = 0)
    {
        $this->scopeConfig->setValue('unific/extension/mode', $mode, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $this->scopeConfig->setValue('unific/extension/interval', $intervalInSeconds, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

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