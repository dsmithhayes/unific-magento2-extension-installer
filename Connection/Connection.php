<?php

namespace Unific\Extension\Connection;

class Connection implements ConnectionInterface
{
    /**
     * Holds the connection instance
     * @var
     */
    protected $connection;

    /**
     * The Object manager that helps us setup different classes
     * @var
     */
    protected $objectManager;

    /**
     * @var \Unific\Extension\Helper\Hmac
     */
    protected $hmacHelper;

    /**
     * @var \Unific\Extension\Model\Server
     */
    protected $serverData;


    protected $scopeConfig;

    protected $queueFactory;

    protected $queueHelper;

    /**
     * Connection constructor.
     *
     * @param \Unific\Extension\Helper\Hmac $hmacHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Unific\Extension\Model\Message\QueueFactory $queueFactory
     */
    public function __construct(
            \Unific\Extension\Helper\Hmac $hmacHelper,
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
            \Unific\Extension\Model\Message\QueueFactory $queueFactory,
            \Unific\Extension\Helper\Message\Queue $queueHelper
    ) {
        $this->hmacHelper = $hmacHelper;
        $this->scopeConfig = $scopeConfig;
        $this->queueFactory = $queueFactory;
        $this->queueHelper = $queueHelper;
    }

    public function setup()
    {
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        return $this;
    }

    /**
     * @param $url
     * @param $jsonExtraHeaders
     * @param $jsonData
     * @param $requestType
     * @param int $responseHttpCode
     * @return bool
     */
    public function queueData($url, $jsonExtraHeaders, $jsonData, $requestType = \Zend_Http_Client::POST, $responseHttpCode = 200)
    {
        $queueModel = $this->queueFactory->create();
        $queueModel->setData(array(
            'url' => $url,
            'headers' => $jsonExtraHeaders,
            'message' => $jsonData,
            'request_type' => $requestType,
            'response_http_code' => $responseHttpCode
        ));
        $queueModel->save();

        return true;
    }

    public function doRequest()
    {
        return $this;
    }

    public function handleResponse()
    {
        return $this;
    }

    /**
     * @return mixed
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param mixed $connection
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return mixed
     */
    public function getObjectManager()
    {
        return $this->objectManager;
    }

    /**
     * @param mixed $objectManager
     */
    public function setObjectManager($objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function getHmacHelper()
    {
        return $this->hmacHelper;
    }

    public function getScopeConfig()
    {
        return $this->scopeConfig;
    }
}