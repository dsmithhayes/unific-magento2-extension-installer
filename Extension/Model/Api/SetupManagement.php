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
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * ModeManagement constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Request\Http $request
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
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
        $this->scopeConfig->setValue('unific/extension/integration', $this->request->getPost('integration_id'), \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        return array('hmac' => array(
            'hmac_header' => $this->scopeConfig->getValue('unific/hmac/hmacHeader', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
            'hmac_secret' => $this->scopeConfig->getValue('unific/hmac/hmacSecret', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
            'hmac_algorithm' => $this->scopeConfig->getValue('unific/hmac/hmacAlgorithm', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
        ));
    }
}