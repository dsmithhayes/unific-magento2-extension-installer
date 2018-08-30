<?php

namespace Unific\Extension\Model\Api\Data;

class Integration implements \Unific\Extension\Api\Data\IntegrationInterface
{
    /**
     * @var
     */
    protected $integration_id;

    /**
     * @return string
     */
    public function getIntegrationId()
    {
        return $this->integration_id;
    }

    /**
     * @param $id
     */
    public function setIntegrationId($id)
    {
        $this->integration_id = $id;
    }
}