<?php

namespace Unific\Extension\Plugin;

class AbstractPlugin
{
    protected $objectManager;

    protected $logger;
    protected $mappingHelper;

    protected $restConnection;

    protected $entity = 'order';
    protected $subject = 'order/create';

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

        $dataModelArray = (is_array($dataModel)) ? $dataModel : $dataModel->getData();

        foreach($data['request_conditions' ] as $condition)
        {
            if($condition['condition_action'] == 'request')
            {
                $actionData = json_decode($condition['condition_action_params'], true);
                $response = $this->restConnection->{$actionData['method']}(
                    $actionData['request_url'],
                    $dataModelArray,
                    array(
                        'X-SUBJECT' => $this->subject
                    )
                );

                $this->logger->info($response->getBody());
            }
        }
    }
}