<?php

namespace Unific\Extension\Plugin;

class OrderPlugin
{
    protected $logger;

    public function __construct(
        \Unific\Extension\Logger\Logger $logger
    )
    {
        $this->logger = $logger;
    }

    public function getRequestCollection()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        return $objectManager->create('\Unific\Extension\Model\ResourceModel\Request\Grid\Collection');
    }

    public function beforePlace($subject, $order)
    {
        foreach ($this->getRequestCollection()
                     ->addFieldToFilter('request_event', array('eq' => 'Magento\Sales\Api\OrderManagementInterface::place'))
                     ->addFieldToFilter('request_event_execution', array('eq' => 'before'))
                 as $id => $request) {
            $this->logger->info('before place order - rule ' . $id);
        }

        return [$order];
    }

    public function afterPlace($subject, $order)
    {
        foreach ($this->getRequestCollection()
                     ->addFieldToFilter('request_event', array('eq' => 'Magento\Sales\Api\OrderManagementInterface::place'))
                     ->addFieldToFilter('request_event_execution', array('eq' => 'after'))
                 as $id => $request) {
            $this->logger->info('after place order - rule ' . $id);
        }

        return $order;
    }
}
