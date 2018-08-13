<?php

namespace Unific\Extension\Connection\Rest;

use Unific\Extension\Connection\ConnectionInterface;

class Connection extends \Unific\Extension\Connection\Connection implements ConnectionInterface
{
    protected $urlData = array();

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

    /**
     * @return mixed|\Unific\Extension\Connection\Connection
     */
    public function doRequest()
    {
        return parent::doRequest();
    }

    /**
     * @return mixed|\Unific\Extension\Connection\Connection
     */
    public function handleResponse()
    {
        return parent::handleResponse();
    }

    /**
     * @param $url
     * @param array $data
     * @param array $extraHeaders
     * @return \Zend_Rest_Client
     */
    public function initConnection($url, $data = array(), $extraHeaders = array())
    {
        $this->urlData = parse_url($url);

        $connection = new \Zend_Rest_Client($this->urlData['scheme'] . '://' . $this->urlData['host']);

        $connection->getHttpClient()->setHeaders(
            //array_merge($extraHeaders, array('X-Magento-Unific-Hmac-SHA256' => $this->getHmacHelper()->generateHmac($data)))
            array_merge($extraHeaders, array('X-Magento-Unific-Hmac-SHA256' => 'test-hmac'))
        );

        return $connection;
    }

    /**
     * @return mixed|string
     */
    public function getRestPath()
    {
        return (isset($this->urlData['query'])) ? $this->urlData['path'] . '?' . urlencode($this->urlData['query']) : $this->urlData['path'];
    }

    /**
     * @param $url
     * @param array $data
     * @param array $extraHeaders
     */
    public function post($url, $data = array(), $extraHeaders = array())
    {
        $this->initConnection($url, $data, $extraHeaders)->restPost($this->urlData['path'], $data);
    }

    /**
     * @param $url
     * @param array $data
     * @param array $extraHeaders
     */
    public function get($url, $data = array(), $extraHeaders = array())
    {
        $this->initConnection($url, $data, $extraHeaders)->restGet($this->urlData['path'], $data);

    }

    /**
     * @param $url
     * @param array $data
     * @param array $extraHeaders
     */
    public function put($url, $data = array(), $extraHeaders = array())
    {
        $this->initConnection($url, $data, $extraHeaders)->restPut($this->urlData['path'], $data);

    }

    /**
     * @param $url
     * @param array $data
     * @param array $extraHeaders
     */
    public function delete($url, $data = array(), $extraHeaders = array())
    {
        $this->initConnection($url, $data, $extraHeaders)->restDelete($this->urlData['path'], $data);
    }
}