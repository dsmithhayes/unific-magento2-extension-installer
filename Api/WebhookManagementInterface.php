<?php

namespace Unific\Extension\Api;

interface WebhookManagementInterface
{
    /**
     * Creates a new webhook
     *
     * @api
     * @param Data\WebhookInterface $webhook
     * @return \Unific\Extension\Api\Data\WebhookInterface
     */
    public function createWebhook(\Unific\Extension\Api\Data\WebhookInterface $webhook);

    /**
     * Updates an existing webhook
     *
     * @api
     *
     * @param Data\WebhookInterface $webhook
     * @return \Unific\Extension\Api\Data\WebhookInterface
     */
    public function updateWebhook(\Unific\Extension\Api\Data\WebhookInterface $webhook);

    /**
     * Deletes an existing webhook
     *
     * @api
     *
     * @param string $uniqueId
     * @return \Unific\Extension\Api\Data\WebhookInterface
     */
    public function deleteWebhook($uniqueId);
}