<?php

namespace Unific\Extension\Connection;

Interface ConnectionInterface
{
    /**
     * Setup the connection so that its ready to send information
     * @return mixed
     */
    public function setup();

    /**
     * Call the requested method
     *
     * @return mixed
     */
    public function doRequest();

    /**
     * Handle response
     * @return mixed
     */
    public function handleResponse();
}