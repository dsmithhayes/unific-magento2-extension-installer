<?php

namespace Unific\Extension\Api;

interface ModeManagementInterface
{
    /**
     * Sets the mode
     *
     * @api
     * @param string $mode burst or live
     *
     * @return bool true on success
     */
    public function setMode($mode);

    /**
     * Returns the mode
     *
     * @api
     *
     * @return string
     */
    public function getMode();
}