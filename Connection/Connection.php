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

    /**
     * Connection constructor.
     *
     * @param \Unific\Extension\Helper\Hmac $hmacHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
            \Unific\Extension\Helper\Hmac $hmacHelper,
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig

    ) {
        $this->hmacHelper = $hmacHelper;
        $this->scopeConfig = $scopeConfig;
    }

    public function setup()
    {
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        return $this;
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