<?php

namespace Unific\Extension\Model\Api;

use Unific\Extension\Api\WebhookeManagementInterface;

class WebhookManagement implements WebhookeManagementInterface
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
     * @return bool true on success
     */
    public function createWebhook()
    {
        $this->webhookHelper->createWebhook($this->request->getPost());
        return true;
    }

    /**
     * Updates an existing webhook
     *
     * @api
     *
     * @return bool true on success
     */
    public function updateWebhook()
    {
        $this->webhookHelper->updateWebhook($this->request->getData());

        return true;
    }

    /**
     * Deletes an existing webhook
     *
     * @api
     *
     * @return bool true on success
     */
    public function deleteWebhook()
    {
        $this->webhookHelper->removeWebhook($this->request->getData());

        return true;
    }
}