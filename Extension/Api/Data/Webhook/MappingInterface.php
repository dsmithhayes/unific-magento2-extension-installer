<?php

namespace Unific\Extension\Api\Data\Webhook;

use Magento\Framework\Api\ExtensibleDataInterface;

interface MappingInterface extends ExtensibleDataInterface
{
    /**
     * @return string
     */
    public function getInternal();

    /**
     * @param string $internal
     * @return void
     */
    public function setInternal($internal);

    /**
     * @return string
     */
    public function getExternal();

    /**
     * @param string $external
     * @return void
     */
    public function setExternal($external);
}