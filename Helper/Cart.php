<?php

namespace Unific\Extension\Helper;

use Magento\Framework\Exception\InputException;
use Symfony\Component\Config\Definition\Exception\Exception;

class Cart extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $logger;
    protected $restConnection;
    protected $scopeConfig;

    /**
     * OrderPlugin constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Unific\Extension\Logger\Logger $logger
     * @param \Unific\Extension\Connection\Rest\Connection $restConnection
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Unific\Extension\Logger\Logger $logger,
        \Unific\Extension\Connection\Rest\Connection $restConnection,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->logger = $logger;
        $this->restConnection = $restConnection;
        $this->scopeConfig = $scopeConfig;
    }

    public function sendCartWebhook()
    {

    }
}
