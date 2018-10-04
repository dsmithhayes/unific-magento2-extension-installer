<?php

namespace Unific\Extension\Model\Api;

use Unific\Extension\Api\ModeManagementInterface;

class ModeManagement implements ModeManagementInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
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
     *
     * @return bool true on success
     */
    public function setMode($mode)
    {
        $this->scopeConfig->setValue('unific/extension/mode', $mode, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

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