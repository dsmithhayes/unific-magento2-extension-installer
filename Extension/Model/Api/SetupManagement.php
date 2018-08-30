<?php

namespace Unific\Extension\Model\Api;

use Unific\Extension\Api\SetupManagementInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class SetupManagement implements SetupManagementInterface
{
    /**
     *  @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $configWriter;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * ModeManagement constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->configWriter = $configWriter;
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
        $this->configWriter->save('unific/extension/integration', $integration->getIntegrationId(), $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeId = 0);

        return array('hmac' => array(
            'hmac_header' => $this->scopeConfig->getValue('unific/hmac/hmacHeader', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
            'hmac_secret' => $this->scopeConfig->getValue('unific/hmac/hmacSecret', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
            'hmac_algorithm' => $this->scopeConfig->getValue('unific/hmac/hmacAlgorithm', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
        ));
    }
}