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
     */
    public function setConditions($conditions);

    /**
     * @return \Unific\Extension\Api\Data\Webhook\MappingInterface[]
     */
    public function getMappings();

    /**
     * @param \Unific\Extension\Api\Data\Webhook\MappingInterface[] $mappings
     */
    public function setMappings($mappings);
}