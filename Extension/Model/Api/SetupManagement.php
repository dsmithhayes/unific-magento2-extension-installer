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
     * ModeManagement constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\Config\ConfigResource\ConfigInterface $configInterface
     * @param \Unific\Extension\Api\Data\HmacInterface $hmacInterface
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface $configInterface,
        \Unific\Extension\Api\Data\HmacInterface $hmacInterface
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->configInterface = $configInterface;
        $this->hmacInterface = $hmacInterface;
    }

    /**
     * Gets the hmac security data
     *
     * @api
     *
     * @return \Unific\Extension\Api\Data\HmacInterface
     */
    public function getData(\Unific\Extension\Api\Data\IntegrationInterface $integration)
    {
        $this->configInterface->saveConfig('unific/extension/integration', $integration->getIntegrationId(), ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);

        $this->hmacInterface->setHmacHeader($this->scopeConfig->getValue('unific/hmac/hmacHeader', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
        $this->hmacInterface->setHmacSecret($this->scopeConfig->getValue('unific/hmac/hmacSecret', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
        $this->hmacInterface->setHmacAlgorithm($this->scopeConfig->getValue('unific/hmac/hmacAlgorithm', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));

        return $this->hmacInterface;
    }
}