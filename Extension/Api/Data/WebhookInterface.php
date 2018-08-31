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
     * @return string
     */
    public function getName();

    /**
     * @param mixed $name
     * @return void
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getUniqueId();

    /**
     * @param string $unique_id
     * @return void
     */
    public function setUniqueId($unique_id);
    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param mixed $description
     * @return void
     */
    public function setDescription($description);

    /**
     * @return string
     */
    public function getEvent();

    /**
     * @param mixed $event
     * @return void
     */
    public function setEvent($event);

    /**
     * @return string
     */
    public function getEventExecution();

    /**
     * @param mixed $eventExecution
     * @return void
     */
    public function setEventExecution($eventExecution);
}