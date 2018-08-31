<?php

namespace Unific\Extension\Model\Api;

use Unific\Extension\Api\WebhookManagementInterface;

class WebhookManagement implements WebhookManagementInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Unific\Extension\Helper\Webhook
     */
    protected $webhookHelper;

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
        \Unific\Extension\Helper\Webhook $webhookHelper,
        \Magento\Framework\App\Request\Http $request
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->webhookHelper = $webhookHelper;
        $this->request = $request;
    }

    /**
     * Creates a new webhook
     *
     * @api
     * @return \Unific\Extension\Api\Data\WebhookInterface
     */
    public function createWebhook(\Unific\Extension\Api\Data\WebhookInterface $webhook)
    {
        $this->webhookHelper->createWebhook($webhook);

        return $webhook;
    }

    /**
     * Updates an existing webhook
     *
     * @api
     *
     * @return \Unific\Extension\Api\Data\WebhookInterface
     */
    public function updateWebhook(\Unific\Extension\Api\Data\WebhookInterface $webhook)
    {
        $this->webhookHelper->updateWebhook($webhook);

        return $webhook;
    }

    /**
     * Deletes an existing webhook
     *
     * @api
     *
     * @return \Unific\Extension\Api\Data\WebhookInterface
     */
    public function deleteWebhook(\Unific\Extension\Api\Data\WebhookInterface $webhook)
    {
        $this->webhookHelper->removeWebhook($webhook);

        return $webhook;
    }
}