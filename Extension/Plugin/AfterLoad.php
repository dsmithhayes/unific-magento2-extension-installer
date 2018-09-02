<?php

namespace Unific\Extension\Plugin;

class AfterLoad
{
    protected $mappingFactory;
    protected $conditionFactory;
    protected $responseMappingFactory;
    protected $responseConditionFactory;

    /**
     * OrderPlugin constructor.
     * @param \Unific\Extension\Model\ResourceModel\Mapping\CollectionFactory $mappingFactory
     * @param \Unific\Extension\Model\ResourceModel\Condition\CollectionFactory $conditionFactory
     * @param \Unific\Extension\Model\ResourceModel\ResponseMapping\CollectionFactory $responseMappingFactory
     * @param \Unific\Extension\Model\ResourceModel\ResponseCondition\CollectionFactory $responseConditionFactory
     */
    public function __construct(
        \Unific\Extension\Model\ResourceModel\Mapping\CollectionFactory $mappingFactory,
        \Unific\Extension\Model\ResourceModel\Condition\CollectionFactory $conditionFactory,
        \Unific\Extension\Model\ResourceModel\ResponseMapping\CollectionFactory $responseMappingFactory,
        \Unific\Extension\Model\ResourceModel\ResponseCondition\CollectionFactory $responseConditionFactory
    )
    {
        $this->mappingFactory = $mappingFactory;
        $this->conditionFactory = $conditionFactory;
        $this->responseMappingFactory = $responseMappingFactory;
        $this->responseConditionFactory = $responseConditionFactory;
    }

    /**
     * Enrich the model with information required to display everything in the admin
     * Its also needed to handle the relations upon receiving or sending a request
     *
     * @param $model
     */
    public function afterLoad($model)
    {
        $extraData = array();

        // Add mappings
        $typeInstance = $this->mappingFactory->create();
        $typeInstance->addFieldToFilter('request_id', $model->getId());

        foreach ($typeInstance->getItems() as $item) {
            $itemData = $item->getData();
	   
	    if(strpos('.', $itemData['internal']) > 0)
	    { 
            	list($itemData['internaltype'], $itemData['internal']) = explode('.', $itemData['internal']);
	    }

            $extraData['request_mappings'][] = $itemData;
        }

        // Add conditions
        $typeInstance = $this->conditionFactory->create();
        $typeInstance->addFieldToFilter('request_id', $model->getId());

        foreach ($typeInstance->getItems() as $item) {
            $itemData = $item->getData();

            $conditionData = array_merge_recursive($item->getData(), array(
                'action_params' => json_decode($itemData['condition_action_params'])
            ));

            $extraData['request_conditions'][] = $conditionData;
        }

        // Add response mappings
        $typeInstance = $this->responseMappingFactory->create();
        $typeInstance->addFieldToFilter('request_id', $model->getId());

        foreach ($typeInstance->getItems() as $item) {
            $itemData = $item->getData();

            if(strpos($itemData['internal'], '.') > 0)
            {
                list($itemData['internaltype'], $itemData['internal']) = explode('.', $itemData['internal']);
            }

            $extraData['response_mappings'][] = $itemData;
        }

        // Add response conditions
        $typeInstance = $this->responseConditionFactory->create();
        $typeInstance->addFieldToFilter('request_id', $model->getId());

        foreach ($typeInstance->getItems() as $item) {
            $itemData = $item->getData();

            $conditionData = array_merge_recursive($item->getData(), array(
                'action_params' => json_decode($itemData['condition_action_params'])
            ));

            $extraData['response_conditions'][] = $conditionData;
        }

        $model->addData($extraData);
    }
}
