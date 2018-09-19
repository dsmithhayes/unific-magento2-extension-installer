<?php

namespace Unific\Extension\Plugin;

class AbstractPlugin
{
    protected $logger;
    protected $mappingHelper;

    protected $restConnection;

    protected $collectionFactory;

    protected $entity = 'order';
    protected $subject = 'order/create';

    /**
     * OrderPlugin constructor.
     * @param \Unific\Extension\Logger\Logger $logger
     * @param \Unific\Extension\Helper\Mapping $mapping
     * @param \Unific\Extension\Connection\Rest\Connection $restConnection
     * @param \Unific\Extension\Model\ResourceModel\Request\Grid\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Unific\Extension\Logger\Logger $logger,
        \Unific\Extension\Helper\Mapping $mapping,
        \Unific\Extension\Connection\Rest\Connection $restConnection,
        \Unific\Extension\Model\ResourceModel\Request\Grid\CollectionFactory $collectionFactory
    )
    {
        $this->logger = $logger;
        $this->mappingHelper = $mapping;
        $this->restConnection = $restConnection;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param string $eventFilter
     * @param string $eventExecution
     * @return mixed
     */
    public function getRequestCollection($eventFilter = 'order/create', $eventExecution = 'after')
    {
        return $this->collectionFactory->create()
            ->addFieldToFilter('request_event', array('eq' => $eventFilter))
            ->addFieldToFilter('request_event_execution', array('eq' => $eventExecution))->load();
    }

    /**
     * @param $id
     * @param $request
     * @param $dataModel
     */
    public function handleCondition($id, $request, $dataModel)
    {
        // A plugin attaches the sub data
        $model = $this->objectManager->create('Unific\Extension\Model\Request');
        $model->load($id);

        $data = $model->getData();

        foreach($data['request_conditions' ] as $condition)
        {
            if($condition['condition_action'] == 'request')
            {
                $actionData = json_decode($condition['condition_action_params'], true);
                $response = $this->restConnection->{$actionData['method']}(
                    $actionData['request_url'],
                    is_array($dataModel) ? $dataModel : $dataModel->getData(),
                    array(
                        'X-SUBJECT' => $this->subject
                    )
                );

                $this->logger->info($response->getBody());
            }
        }
    }
}