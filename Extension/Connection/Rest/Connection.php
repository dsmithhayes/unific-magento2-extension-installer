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
     * @return \Zend_Http_Client
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

        $extraHeaders["Content-Type"] = 'application/json';
        $this->connection->setNoReset(true);

        $client = $this->connection->getHttpClient();
        $client->setUri($url);
        $client->setHeaders($extraHeaders);

        return $client;
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
        $result = $this->initConnection($url, $data, $extraHeaders)->setRawData(json_encode($data))->request(\Zend_Http_Client::POST);

        return $this->connection->getHttpClient()->getLastResponse();
    }

    /**
     * @param $url
     * @param array $data
     * @param array $extraHeaders
     */
    public function get($url, $data = array(), $extraHeaders = array())
    {
        $result = $this->initConnection($url, $data, $extraHeaders)->setParameterGet($data)->request(\Zend_Http_Client::GET);

        return $this->connection->getHttpClient()->getLastResponse();
    }

    /**
     * @param $url
     * @param array $data
     * @param array $extraHeaders
     */
    public function put($url, $data = array(), $extraHeaders = array())
    {
        $result = $this->initConnection($url, $data, $extraHeaders)->setRawData(json_encode($data))->request(\Zend_Http_Client::PUT);

        return $this->connection->getHttpClient()->getLastResponse();
    }

    /**
     * @param $url
     * @param array $data
     * @param array $extraHeaders
     */
    public function delete($url, $data = array(), $extraHeaders = array())
    {
        $result = $this->initConnection($url, $data, $extraHeaders)->setRawData(json_encode($data))->request(\Zend_Http_Client::DELETE);

        return $this->connection->getHttpClient()->getLastResponse();
    }
}