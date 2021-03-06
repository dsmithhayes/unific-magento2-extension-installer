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
        $this->webhookHelper->saveWebhook($webhook);

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
        $this->webhookHelper->saveWebhook($webhook);

        return $webhook;
    }

    /**
     * Deletes an existing webhook
     *
     * @api
     *
     * @param string $uniqueId
     * @return \Unific\Extension\Api\Data\WebhookInterface
     */
    public function deleteWebhook($uniqueId)
    {
        $this->webhookHelper->removeWebhook($uniqueId);

        return $uniqueId;
    }

    /**
     * Deletes all existing webhooks
     *
     * @api
     *
     * @return \Unific\Extension\Api\Data\WebhookInterface
     */
    public function deleteAllWebhooks()
    {
        $this->webhookHelper->removeAllWebhooks();

        return true;
    }
}