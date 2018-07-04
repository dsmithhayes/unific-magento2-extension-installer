<?php

namespace Unific\Extension\Api;

interface WebhookManagementInterface
{
    /**
     * Sets the group of a webhook
     *
     * @api
     * @param string $group
     * @return bool true on success
     */
    public function setGroup($group = 'default');

    /**
     * Returns the mode
     *
     * @api
     *
     * @return string
     */
    public function getGroup();
}