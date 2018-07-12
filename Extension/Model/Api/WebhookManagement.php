<?php

namespace Unific\Extension\Model\Api;

use Unific\Extension\Api\WebhookeManagementInterface;

class WebhookManagement implements WebhookeManagementInterface
{
    protected $scopeConfig;

    protected $webhookHelper;

    /**
     * ModeManagement constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Unific\Extension\Helper\Webhook $webhookHelper
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->webhookHelper = $webhookHelper;
    }
}