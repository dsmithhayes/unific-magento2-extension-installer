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

        $this->connection = new \Zend_Rest_Client($this->urlData['scheme'] . '://' . $this->urlData['host']);

        $integrationKey = $this->scopeConfig->getValue('unific/extension/integration_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $extraHeaders[$integrationKey] =  $this->scopeConfig->getValue('unific/extension/integration', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if($this->scopeConfig->getValue('unific/hmac/hmacEnable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE))
        {
            $hmacKey = $this->scopeConfig->getValue('unific/hmac/hmacHeader', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $extraHeaders[$hmacKey] = $this->hmacHelper->generateHmac($data);
        }

        // Always send json
        $extraHeaders['Content-type'] = 'application/json';

        $this->connection->getHttpClient()->setHeaders($extraHeaders);

        return $this->connection;
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
        $result = $this->initConnection($url, $data, $extraHeaders)->restPost($this->urlData['path'], $data);

        return $this->connection->getHttpClient()->getLastResponse();
    }

    /**
     * @param $url
     * @param array $data
     * @param array $extraHeaders
     */
    public function get($url, $data = array(), $extraHeaders = array())
    {
        $result = $this->initConnection($url, $data, $extraHeaders)->restGet($this->urlData['path'], $data);

        return $this->connection->getHttpClient()->getLastResponse();
    }

    /**
     * @param $url
     * @param array $data
     * @param array $extraHeaders
     */
    public function put($url, $data = array(), $extraHeaders = array())
    {
        $result = $this->initConnection($url, $data, $extraHeaders)->restPut($this->urlData['path'], $data);

        return $this->connection->getHttpClient()->getLastResponse();
    }

    /**
     * @param $url
     * @param array $data
     * @param array $extraHeaders
     */
    public function delete($url, $data = array(), $extraHeaders = array())
    {
        $result = $this->initConnection($url, $data, $extraHeaders)->restDelete($this->urlData['path'], $data);

        return $this->connection->getHttpClient()->getLastResponse();
    }
}