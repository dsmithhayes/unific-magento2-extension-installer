<?php

namespace Unific\Extension\Api;

interface WebhookManagementInterface
{
    /**
     * Creates a new webhook
     *
     * @api
     * @return \Unific\Extension\Api\Data\WebhookInterface
     */
    public function createWebhook();

    /**
     * Updates an existing webhook
     *
     * @api
     *
     * @return \Unific\Extension\Api\Data\WebhookInterface
     */
    public function updateWebhook();

    /**
     * Deletes an existing webhook
     *
     * @api
     *
     * @return \Unific\Extension\Api\Data\WebhookInterface
     */
    public function deleteWebhook();
}