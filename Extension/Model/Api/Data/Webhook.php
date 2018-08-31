<?php

namespace Unific\Extension\Model\Api\Data;

class Webhook implements \Unific\Extension\Api\Data\WebhookInterface
{
    /**
     * @var
     */
    protected $name;

    /**
     * @var
     */
    protected $unique_id;

    /**
     * @var
     */
    protected $group;

    /**
     * @var
     */
    protected $description;

    /**
     * @var
     */
    protected $event;

    /**
     * @var
     */
    protected $eventExecution;

    /**
     * @var \Unific\Extension\Api\Data\Webhook\ConditionInterface[]
     */
    protected $conditions = array();

    /**
     * @var \Unific\Extension\Api\Data\Webhook\MappingInterface[]
     */
    protected $mappings = array();

    /**
     * @var \Unific\Extension\Api\Data\Webhook\ResponseInterface
     */
    protected $response;

    /**
     * @return array
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * @param \Unific\Extension\Api\Data\Webhook\ConditionInterface[] $conditions
     */
    public function setConditions(array $conditions)
    {
        $this->conditions = $conditions;
    }

    /**
     * @return array
     */
    public function getMappings()
    {
        return $this->mappings;
    }

    /**
     * @param \Unific\Extension\Api\Data\Webhook\MappingInterface[] $mappings
     */
    public function setMappings(array $mappings)
    {
        $this->mappings = $mappings;
    }

    /**
     * @return \Unific\Extension\Api\Data\Webhook\ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param \Unific\Extension\Api\Data\Webhook\ResponseInterface $response
     */
    public function setResponse(\Unific\Extension\Api\Data\Webhook\ResponseInterface $response)
    {
        $this->response = $response;
    }


    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getUniqueId()
    {
        return $this->unique_id;
    }

    /**
     * @param string $unique_id
     */
    public function setUniqueId($unique_id)
    {
        $this->unique_id = $unique_id;
    }

    /**
     * @return mixed
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param string $group
     */
    public function setGroup($group = "Default")
    {
        $this->group = $group;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param mixed $event
     */
    public function setEvent($event)
    {
        $this->event = $event;
    }

    /**
     * @return mixed
     */
    public function getEventExecution()
    {
        return $this->eventExecution;
    }

    /**
     * @param mixed $eventExecution
     */
    public function setEventExecution($eventExecution)
    {
        $this->eventExecution = $eventExecution;
    }
}