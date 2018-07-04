<?php

namespace Unific\Extension\Plugin;

class AfterLoad
{
    /**
     * Enrich the model with information required to display everything in the admin
     * Its also needed to handle the relations upon receiving or sending a request
     *
     * @param $model
     */
    public function afterLoad($model)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $extraData = array();

        // Add mappings
        $typeInstance = $objectManager->get('Unific\Extension\Model\ResourceModel\Mapping\Collection');
        $typeInstance->addFieldToFilter('request_id', $model->getId());

        foreach ($typeInstance->getItems() as $item) {
            $itemData = $item->getData();

            list($itemData['internaltype'], $itemData['internal']) = explode('.', $itemData['internal']);
            $extraData['request_mappings'][] = $itemData;
        }

        // Add conditions
        $typeInstance = $objectManager->get('Unific\Extension\Model\ResourceModel\Condition\Collection');
        $typeInstance->addFieldToFilter('request_id', $model->getId());

        foreach ($typeInstance->getItems() as $item) {
            $itemData = $item->getData();

            $conditionData = array_merge_recursive($item->getData(), array(
                'action_params' => json_decode($itemData['condition_action_params'])
            ));

            $extraData['request_conditions'][] = $conditionData;
        }

        // Add response mappings
        $typeInstance = $objectManager->get('Unific\Extension\Model\ResourceModel\ResponseMapping\Collection');
        $typeInstance->addFieldToFilter('request_id', $model->getId());

        foreach ($typeInstance->getItems() as $item) {
            $itemData = $item->getData();
            list($itemData['internaltype'], $itemData['internal']) = explode('.', $itemData['internal']);
            $extraData['response_mappings'][] = $itemData;
        }

        // Add response conditions
        $typeInstance = $objectManager->get('Unific\Extension\Model\ResourceModel\ResponseCondition\Collection');
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
