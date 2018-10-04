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
     * @param \Unific\Extension\Model\ResourceModel\Message\Queue\CollectionFactory $queueCollectionFactory
     * @param \Unific\Extension\Connection\Rest\Connection $restConnection
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Unific\Extension\Logger\Logger $logger,
        \Unific\Extension\Model\ResourceModel\Message\Queue\CollectionFactory $queueCollectionFactory,
        \Unific\Extension\Connection\Rest\Connection $restConnection
    )
    {
        $this->logger = $logger;
        $this->restConnection = $restConnection;
        $this->queueCollectionFactory = $queueCollectionFactory;
    }

    public function process()
    {
        return;

        // Every time this triggers, process 100 entities from the message queue
        // Then send 10 historical entries too, which have 10 entities in them
        $queueData = $this->popDataFromQueue(false, 100);
        $queueData = array_merge($queueData, $this->popDataFromQueue(true, 10));

        if(count($queueData) > 0)
        {
            foreach($queueData as $queueItem)
            {
                $this->restConnection->sendData($queueItem['url'], json_decode($queueItem['message'], true), json_decode($queueItem['headers'], $queueItem['request_type']));
            }
        }
    }

    /**
     * @param bool $isHistorical
     * @param int $size
     * @return mixed
     */
    protected function popDataFromQueue($isHistorical = false, $size = 100)
    {
        $collection = $this->queueCollectionFactory->create();
        $collection->addFieldToFilter('historical', array('eq', (int) $isHistorical));

        $returnData = array();

        if($collection->getSize() > 0)
        {
            $collection->setPageSize($size);
            $collection->setCurPage(1);
            $collection->load();

            $returnData = $collection->getData();

            // Remove it from the database for now
            $collection->walk('delete');
        }


        return $returnData;
    }
}
