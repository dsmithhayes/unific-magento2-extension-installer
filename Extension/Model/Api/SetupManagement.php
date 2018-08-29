<?php

namespace Unific\Extension\Model\Api;

use Unific\Extension\Api\SetupManagementInterface;

class SetupManagement implements SetupManagementInterface
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
     * Gets the hmac security data
     *
     * @api
     *
     * @return array
     */
    public function getData()
    {
        return array(
            'hmac_header' => $this->scopeConfig->getValue('unific/hmac/hmacHeader', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
            'hmac_secret' => $this->scopeConfig->getValue('unific/hmac/hmacSecret', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
            'hmac_algorithm' => $this->scopeConfig->getValue('unific/hmac/hmacAlgorithm', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
        );
    }
}