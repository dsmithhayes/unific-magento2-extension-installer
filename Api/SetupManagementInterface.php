<?php

namespace Unific\Extension\Api;

interface SetupManagementInterface
{
    /**
     * Returns the connection data
     *
     * @api
     *
     * @param Data\IntegrationInterface $integration
     * @return \Unific\Extension\Api\Data\SetupResponseInterface
     */
    public function getData(\Unific\Extension\Api\Data\IntegrationInterface $integration);
}