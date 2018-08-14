<?php

namespace Unific\Extension\Helper;

use Magento\Framework\Exception\InputException;
use Symfony\Component\Config\Definition\Exception\Exception;

class Queue extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $logger;
    protected $restConnection;
    protected $queueCollection;

    /**
     * OrderPlugin constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Unific\Extension\Logger\Logger $logger
     * @param \Unific\Extension\Model\ResourceModel\Message\Queue\Collection $queueCollection
     * @param \Unific\Extension\Connection\Rest\Connection $restConnection
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Unific\Extension\Logger\Logger $logger,
        \Unific\Extension\Model\Message\Queue $queueCollection,
        \Unific\Extension\Connection\Rest\Connection $restConnection
    )
    {
        $this->logger = $logger;
        $this->restConnection = $restConnection;
        $this->queueCollection = $queueCollection;
    }

    public function process()
    {
    }
}
