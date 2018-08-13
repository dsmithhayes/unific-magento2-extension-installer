<?php

namespace Unific\Extension\Plugin;

class OrderPlugin
{
    protected $objectManager;

    protected $logger;
    protected $mappingHelper;

    protected $restConnection;

    /**
     * OrderPlugin constructor.
     * @param \Unific\Extension\Logger\Logger $logger
     * @param \Unific\Extension\Helper\Mapping $mapping
     * @param \Unific\Extension\Connection\Rest\Connection $restConnection
     */
    public function __construct(
        \Unific\Extension\Logger\Logger $logger,
        \Unific\Extension\Helper\Mapping $mapping,
        \Unific\Extension\Connection\Rest\Connection $restConnection
    )
    {
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $this->logger = $logger;
        $this->mappingHelper = $mapping;
        $this->restConnection = $restConnection;
    }

    /**
     * @return mixed
     */
    public function getRequestCollection()
    {
        return $this->objectManager->create('\Unific\Extension\Model\ResourceModel\Request\Grid\Collection');
    }

    /**
     * @param $subject
     * @param $order
     * @return array
     */
    public function beforePlace($subject, $order)
    {
        foreach ($this->getRequestCollection()
                     ->addFieldToFilter('request_event', array('eq' => 'Magento\Sales\Api\OrderManagementInterface::place'))
                     ->addFieldToFilter('request_event_execution', array('eq' => 'before'))
                 as $id => $request) {

            // A plugin attaches the sub data
            $model = $this->objectManager->create('Unific\Extension\Model\Request');
            $model->load($id);
        }

        return [$order];
    }

    /**
     * @param $subject
     * @param $order
     * @return mixed
     */
    public function afterPlace($subject, $order)
    {
        foreach ($this->getRequestCollection()
                     ->addFieldToFilter('request_event', array('eq' => 'Magento\Sales\Api\OrderManagementInterface::place'))
                     ->addFieldToFilter('request_event_execution', array('eq' => 'after'))
                 as $id => $request) {

            // A plugin attaches the sub data
            $model = $this->objectManager->create('Unific\Extension\Model\Request');
            $model->load($id);

            $data = $model->getData();

            foreach($data['request_conditions' ] as $condition)
            {
                if($condition['condition_action'] == 'request')
                {
                    $actionData = json_decode($condition['condition_action_params'], true);
                    $this->restConnection->{$actionData['method']}(
                        $actionData['request_url'],
                        $this->mappingHelper->map($order->getData(), 'order'),
                        array(
                            'X-SUBJECT' => 'order/create'
                        )
                    );
                }
            }
        }

        return $order;
    }
}
