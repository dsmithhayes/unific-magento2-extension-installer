<?php

namespace Unific\Extension\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface WebhookInterface extends ExtensibleDataInterface
{
    /**
     * @return \Unific\Extension\Api\Data\Webhook\ConditionInterface[]
     */
    public function getConditions();

    /**
     * @param \Unific\Extension\Api\Data\Webhook\ConditionInterface[] $conditions
     * @return void
     */
    public function setConditions(array $conditions);

    /**
     * @return \Unific\Extension\Api\Data\Webhook\MappingInterface[]
     */
    public function getMappings();

    /**
     * @param \Unific\Extension\Api\Data\Webhook\MappingInterface[] $mappings
     * @return void
     */
    public function setMappings(array $mappings);

    /**
     * @return \Unific\Extension\Api\Data\Webhook\ResponseInterface
     */
    public function getResponse();

    /**
     * @param \Unific\Extension\Api\Data\Webhook\ResponseInterface $response
     * @return void
     */
    public function setResponse(\Unific\Extension\Api\Data\Webhook\ResponseInterface $response);


    /**
     * @return mixed
     */
    public function getName();

    /**
     * @param mixed $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getUniqueId();

    /**
     * @param string $unique_id
     */
    public function setUniqueId($unique_id);
    /**
     * @return mixed
     */
    public function getDescription();

    /**
     * @param mixed $description
     */
    public function setDescription($description);

    /**
     * @return mixed
     */
    public function getEvent();

    /**
     * @param mixed $event
     */
    public function setEvent($event);

    /**
     * @return mixed
     */
    public function getEventExecution();

    /**
     * @param mixed $eventExecution
     */
    public function setEventExecution($eventExecution);
}