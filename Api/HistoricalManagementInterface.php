<?php

namespace Unific\Extension\Api;

interface HistoricalManagementInterface
{
    /**
     * Triggers the historical process
     *
     * @api
     *
     * @return bool true on success
     */
    public function setTrigger();
}