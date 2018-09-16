<?php

namespace Unific\Extension\Model\Api\Data\Webhook;

class Response implements \Unific\Extension\Api\Data\Webhook\ResponseInterface
{
    /**
     * @var \Unific\Extension\Api\Data\Webhook\ConditionInterface[]
     */
    protected $conditions = array();

    /**
     * @var \Unific\Extension\Api\Data\Webhook\MappingInterface[]
     */
    protected $mappings = array();

    /**
     * @return \Unific\Extension\Api\Data\Webhook\ConditionInterface[]
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * @param \Unific\Extension\Api\Data\Webhook\ConditionInterface[] $conditions
     */
    public function setConditions($conditions)
    {
        $this->conditions = $conditions;
    }

    /**
     * @return \Unific\Extension\Api\Data\Webhook\MappingInterface[]
     */
    public function getMappings()
    {
        return $this->mappings;
    }

    /**
     * @param \Unific\Extension\Api\Data\Webhook\MappingInterface[] $mappings
     */
    public function setMappings($mappings)
    {
        $this->mappings = $mappings;
    }
}