<?php

namespace Unific\Extension\Connection\Soap;

use Unific\Extension\Connection\ConnectionInterface;

class Connection extends \Unific\Extension\Connection\Connection implements ConnectionInterface
{
    /**
     * Setup the initial connection
     * @return $this
     */
    public function setup()
    {
        parent::setup();

        $this->connection = $this->getObjectManager()->create('Zend_Soap_Client');

        return $this;
    }

    public function doRequest()
    {
        return parent::doRequest();
    }

    public function handleResponse()
    {
        return parent::handleResponse();
    }
}