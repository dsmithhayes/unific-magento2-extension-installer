<?php
namespace Unific\Extension\Api;

interface ModeManagementInterface
{
    /**
     * Sets the mode
     *
     * @api
     * @param string $mode burst or live
     * @param int $intervalInSeconds when in burst mode
     *
     * @return bool true on success
     */
    public function setMode($mode, $intervalInSeconds = 0);

    /**
     * Returns the mode
     *
     * @api
     *
     * @return string
     */
    public function getMode();
}