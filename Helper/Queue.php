<?php

namespace Unific\Extension\Helper;

use Magento\Framework\Exception\InputException;
use Symfony\Component\Config\Definition\Exception\Exception;

class Queue extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $logger;
    protected $restConnection;
    protected $queueCollectionFactory;

    /**
     * OrderPlugin constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Unific\Extension\Logger\Logger $logger
     * @param \Unific\Extension\Model\ResourceModel\Queue\CollectionFactory $queueCollectionFactory
     * @param \Unific\Extension\Connection\Rest\Connection $restConnection
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Unific\Extension\Logger\Logger $logger,
        \Unific\Extension\Model\ResourceModel\Queue\CollectionFactory $queueCollectionFactory,
        \Unific\Extension\Connection\Rest\Connection $restConnection
    )
    {
        $this->logger = $logger;
        $this->restConnection = $restConnection;
        $this->queueCollectionFactory = $queueCollectionFactory;
    }

    public function process()
    {
        $this->logger->info('Processing message queue');

        // Every time this triggers, process 100 entities from the message queue
        // Then send 10 historical entries too, which have 10 entities in them
        $this->sendDataFromQueue(false, 100);
        $this->sendDataFromQueue(true, 10);

        return true;
    }

    /**
     * @param bool $isHistorical
     * @param int $size
     */
    protected function sendDataFromQueue($isHistorical = false, $size = 100)
    {
        $collection = $this->queueCollectionFactory->create();
        $collection->addFieldToFilter('historical', array('eq', (int) $isHistorical));

        $this->logger->info('Queue size: ' . $collection->getSize());

        if($collection->getSize() > 0)
        {
            $collection->setPageSize($size);
            $collection->setCurPage(1);
            $collection->load();

            foreach($collection->getAllItems() as $queueItem)
            {
                $this->restConnection->sendData($queueItem->getUrl(), json_decode($queueItem->getMessage(), true), json_decode($queueItem->getHeaders()), strtoupper($queueItem->getRequestType()));
            }

            // Remove it from the database for now
            $collection->walk('delete');
        } else {
            $this->logger->info('nothing to send');
        }
    }
}
