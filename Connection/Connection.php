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
     * @param \Unific\Extension\Model\QueueFactory $queueFactory
     * @param \Unific\Extension\Helper\Message\Queue $queueHelper
     */
    public function __construct(
            \Unific\Extension\Helper\Hmac $hmacHelper,
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
            \Unific\Extension\Model\QueueFactory $queueFactory,
            \Unific\Extension\Helper\Message\Queue $queueHelper
    ) {
        $this->hmacHelper = $hmacHelper;
        $this->scopeConfig = $scopeConfig;
        $this->queueFactory = $queueFactory;
        $this->queueHelper = $queueHelper;
    }

    public function setup()
    {
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

    public function getHmacHelper()
    {
        return $this->hmacHelper;
    }

    public function getScopeConfig()
    {
        return $this->scopeConfig;
    }
}