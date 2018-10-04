<?php

namespace Unific\Extension\Model\Message;

use Magento\Framework\Model\AbstractModel;
use Unific\Extension\Api\Data\QueueInterface;

class Queue extends AbstractModel implements QueueInterface
{
    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Unific\Extension\Model\ResourceModel\Message\Queue');
    }

    /**
     * @return string
     */
    public function getGuid()
    {
        return $this->_getData('guid');
    }

    /**
     * @param string $guid
     * @return void
     */
    public function setGuid($guid)
    {
        $this->setData('guid', $guid);
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->_getData('message');
    }

    /**
     * @param $message
     * @return void
     */
    public function setMessage($message)
    {
        $this->setData('message', $message);
    }

    /**
     * @return string
     */
    public function getHeaders()
    {
        return $this->_getData('headers');
    }

    public function setHeaders($headers)
    {
        $this->setData('headers', $headers);
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->_getData('url');
    }

    /**
     * @param $url
     * @return void
     */
    public function setUrl($url)
    {
        $this->setData('url', $url);
    }

    /**
     * @return int
     */
    public function getHistorical()
    {
        return $this->_getData('historical');
    }

    /**
     * @param bool $historical
     * @return void
     */
    public function setHistorical($historical = false)
    {
        $this->setData('historical', $historical);
    }

    /**
     * @return string
     */
    public function getRequestType()
    {
        return $this->_getData('request_type');
    }

    /**
     * @param string $requestType
     * @return void
     */
    public function setRequestType($requestType = 'get')
    {
        $this->setData('request_type', $requestType);
    }

    /**
     * @return int
     */
    public function getRetryAmount()
    {
        return $this->_getData('retry_amount');
    }

    /**
     * @param int $retryAmount
     * @return void
     */
    public function setRetryAmount($retryAmount = 0)
    {
        $this->setData('retry_amount', $retryAmount);
    }

    /**
     * @return int
     */
    public function getMaxRetryAmount()
    {
        return $this->_getData('max_retry_amount');
    }

    /**
     * @param int $maxRetryAmount
     * @return void
     */
    public function setMaxRetryAmount($maxRetryAmount = 20)
    {
        $this->setData('max_retry_amount', $maxRetryAmount);
    }

    /**
     * @return string
     */
    public function getResponseError()
    {
        return $this->_getData('response_error');
    }

    /**
     * @param string $responseError
     * @return void
     */
    public function setResponseError($responseError = '')
    {
        $this->setData('response_error', $responseError);
    }

    /**
     * @return int
     */
    public function getResponseHttpCode()
    {
        return $this->_getData('response_http_code');
    }

    /**
     * @param int $responseHttpCode
     * @return void
     */
    public function setResponseHttpCode($responseHttpCode = 200)
    {
        $this->setData('response_http_code', $responseHttpCode);
    }

    /**
     * @return int
     */
    public function getRequestDateFirst()
    {
        return $this->_getData('request_date_first');
    }

    /**
     * @param $dateFirst
     * @return void
     */
    public function setRequestDateFirst($dateFirst)
    {
        $this->setData('request_date_first', $dateFirst);
    }

    /**
     * @return int
     */
    public function getRequestDateLast()
    {
        return $this->_getData('request_date_last');
    }

    /**
     * @param $dateLast
     * @return void
     */
    public function setRequestDateLast($dateLast)
    {
        $this->setData('request_date_last', $dateLast);
    }
}