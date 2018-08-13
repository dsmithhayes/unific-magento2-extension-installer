<?php

namespace Unific\Extension\Connection\Rest;

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

        $this->connection = $this->getObjectManager()->create('Zend_Rest_Client');

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

    public function post($url, $data = array(), $extraHeaders = array())
    {
        $urlData = parse_url($url);

        $connection = new \Zend_Rest_Client($urlData['scheme'] . '://' . $urlData['host']);

        $connection->getHttpClient()->setHeaders(
            //array_merge($extraHeaders, array('X-Magento-Unific-Hmac-SHA256' => $this->getHmacHelper()->generateHmac($data)))
            array_merge($extraHeaders, array('X-Magento-Unific-Hmac-SHA256' => 'test-hmac'))
        );

        $connection->restPost($urlData['path'], $data);
    }
}