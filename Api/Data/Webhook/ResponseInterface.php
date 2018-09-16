<?php

namespace Unific\Extension\Api\Data\Webhook;

use Magento\Framework\Api\ExtensibleDataInterface;

interface ResponseInterface extends ExtensibleDataInterface
{
    /**
     * @return \Unific\Extension\Api\Data\Webhook\ConditionInterface[]
     */
    public function getConditions();

    /**
     * @param \Unific\Extension\Api\Data\Webhook\ConditionInterface[] $conditions
     * @return void
     */
    public function setConditions($conditions);

    /**
     * @return \Unific\Extension\Api\Data\Webhook\MappingInterface[]
     */
    public function getMappings();

    /**
     * @param \Unific\Extension\Api\Data\Webhook\MappingInterface[] $mappings
     * @return void
     */
    public function setMappings($mappings);
}