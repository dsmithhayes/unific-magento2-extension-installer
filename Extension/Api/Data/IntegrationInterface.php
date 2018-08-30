<?php

namespace Unific\Extension\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface IntegrationInterface extends ExtensibleDataInterface
{
    /**
     * @return string
     */
    public function getIntegrationId();

    /**
     * @param $id
     * @return void
     */
    public function setIntegrationId($id);
}