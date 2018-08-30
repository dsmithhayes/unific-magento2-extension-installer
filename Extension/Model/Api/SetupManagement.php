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
     * ModeManagement constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\Config\ConfigResource\ConfigInterface $configInterface
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface $configInterface
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->configInterface = $configInterface;
    }

    /**
     * Gets the hmac security data
     *
     * @api
     *
     * @return array
     */
    public function getData(\Unific\Extension\Api\Data\IntegrationInterface $integration)
    {
        $this->configInterface->saveConfig('unific/extension/integration', $integration->getIntegrationId(), ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);

        return array('hmac' => array(
            'hmac_header' => $this->scopeConfig->getValue('unific/hmac/hmacHeader', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
            'hmac_secret' => $this->scopeConfig->getValue('unific/hmac/hmacSecret', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
            'hmac_algorithm' => $this->scopeConfig->getValue('unific/hmac/hmacAlgorithm', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
        ));
    }
}