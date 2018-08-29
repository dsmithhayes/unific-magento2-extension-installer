<?php

namespace Unific\Extension\Api;

interface WebhookManagementInterface
{
    /**
     * Creates a new webhook
     *
     * @api
     * @return bool true on success
     */
    public function createWebhook();

    /**
     * Updates an existing webhook
     *
     * @api
     *
     * @return bool true on success
     */
    public function updateWebhook();

    /**
     * Deletes an existing webhook
     *
     * @api
     *
     * @return bool true on success
     */
    public function deleteWebhook();
}