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

        if($collection->getSize() > 0)
        {
            $collection->setPageSize($size);
            $collection->setCurPage(1);

            foreach($collection as $queueItem)
            {
                $this->logger->info('trying to send: ' . $queueItem->getGuid());

                switch($queueItem->getRequestType())
                {
                    case 'post':
                        $type = \Zend_Http_Client::POST;
                        break;
                    case 'put':
                        $type = \Zend_Http_Client::PUT;
                        break;
                    case 'delete':
                        $type = \Zend_Http_Client::DELETE;
                        break;
                    default:
                        $type = \Zend_Http_Client::GET;
                        break;
                }

                $this->restConnection->sendData($queueItem->getUrl(), json_decode($queueItem->getMessage(), true), json_decode($queueItem->getHeaders()), $type);
            }

            // Remove it from the database for now
            $collection->walk('delete');
        } else {
            $this->logger->info('nothing to send');
        }
    }
}
