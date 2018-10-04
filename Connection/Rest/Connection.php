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
     * @param bool $queue
     * @param $requestType
     * @param bool $historical
     * @return mixed
     */
    public function executeRequest($url, $data = array(), $extraHeaders = array(), $queue = false, $requestType = \Zend_Http_Client::POST, $historical = false)
    {
        // Ensure the request is always sent to the queue if its in burst mode
        if($queue || $this->scopeConfig->getValue('unific/extension/mode') == 'burst')
        {
            return $this->queueHelper->queue($url, $data, $extraHeaders, $requestType, $historical);
        } else {
            return $this->sendData($url, $data, $extraHeaders, $requestType);
        }
    }

    /**
     * @param $url
     * @param array $data
     * @param array $extraHeaders
     * @param bool $queue
     * @param $requestType
     * @return bool
     */
    public function sendData($url, $data = array(), $extraHeaders = array(), $requestType = \Zend_Http_Client::POST)
    {
        if($requestType == \Zend_Http_Client::GET)
        {
            $this->initConnection($url, $data, $extraHeaders)->setParameterGet($data)->request($requestType);
        } else {
            $this->initConnection($url, $data, $extraHeaders)->setRawData(json_encode($data))->request($requestType);
        }

        // @todo If last response is not in 200 range, (re) queue the data

        return $this->connection->getHttpClient()->getLastResponse();
    }

    /**
     * @param $url
     * @param array $data
     * @param array $extraHeaders
     * @param bool $queue
     * @return
     */
    public function post($url, $data = array(), $extraHeaders = array(), $queue = false)
    {
        return $this->executeRequest($url,  $data, $extraHeaders, $queue, \Zend_Http_Client::POST);
    }

    /**
     * @param $url
     * @param array $data
     * @param array $extraHeaders
     * @param bool $queue
     * @return
     */
    public function get($url, $data = array(), $extraHeaders = array(), $queue = false)
    {
        return $this->executeRequest($url, $data, $extraHeaders, $queue, \Zend_Http_Client::GET);
    }

    /**
     * @param $url
     * @param array $data
     * @param array $extraHeaders
     * @param bool $queue
     * @return
     */
    public function put($url, $data = array(), $extraHeaders = array(), $queue = false)
    {
        return $this->executeRequest($url, $data, $extraHeaders, $queue, \Zend_Http_Client::PUT);
    }

    /**
     * @param $url
     * @param array $data
     * @param array $extraHeaders
     * @param bool $queue
     * @return
     */
    public function delete($url, $data = array(), $extraHeaders = array(), $queue = false)
    {
        return $this->executeRequest($url, $data, $extraHeaders, $queue, \Zend_Http_Client::DELETE);
    }
}