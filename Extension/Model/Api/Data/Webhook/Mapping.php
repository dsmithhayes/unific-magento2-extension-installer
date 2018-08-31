<?php

namespace Unific\Extension\Model\Api\Data\Webhook;

class Mapping implements \Unific\Extension\Api\Data\Webhook\MappingInterface
{
    protected $internal = '';
    protected $external = '';

    /**
     * @return string
     */
    public function getInternal()
    {
        return $this->internal;
    }

    /**
     * @param string $internal
     */
    public function setInternal($internal)
    {
        $this->internal = $internal;
    }

    /**
     * @return string
     */
    public function getExternal()
    {
        return $this->external;
    }

    /**
     * @param string $external
     */
    public function setExternal($external)
    {
        $this->external = $external;
    }
}