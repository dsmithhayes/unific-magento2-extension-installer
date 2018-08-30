<?php

namespace Unific\Extension\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface WebhookInterface extends ExtensibleDataInterface
{
    /**
     * @return string
     */
    public function getGroup();

    /**
     * @param string $group
     * @return void
     */
    public function setGroup($group = 'default');

    /**
     * @return string
     */
    public function getName();

    /**
     * @param $name
     * @return void
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     * @return void
     */
    public function setDescription($description = 'No description available');

    /**
     * @return string
     */
    public function getUniqueId();

    /**
     * @param $uniqueId
     * @return void
     */
    public function setUniqueId($uniqueId);
}