<?php

namespace Unific\Extension\Helper;

use Magento\Framework\Exception\InputException;
use Symfony\Component\Config\Definition\Exception\Exception;

class Webhook extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $scopeConfig;

    protected $requestModel;
    protected $mappingModel;
    protected $conditionModel;
    protected $responseConditionModel;
    protected $responseMappingModel;

    /**
     * Request constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Unific\Extension\Model\Request $requestModel
     * @param \Unific\Extension\Model\Mapping $mappingModel
     * @param \Unific\Extension\Model\Condition $conditionModel
     * @param \Unific\Extension\Model\ResponseCondition $responseConditionModel
     * @param \Unific\Extension\Model\ResponseMapping $responseMappingModel
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Unific\Extension\Model\Request $requestModel,
        \Unific\Extension\Model\Mapping $mappingModel,
        \Unific\Extension\Model\Condition $conditionModel,
        \Unific\Extension\Model\ResponseCondition $responseConditionModel,
        \Unific\Extension\Model\ResponseMapping $responseMappingModel)
    {
        parent::__construct($context);

        $this->scopeConfig = $scopeConfig;
        $this->requestModel = $requestModel;
        $this->mappingModel = $mappingModel;
        $this->conditionModel = $conditionModel;
        $this->responseConditionModel = $responseConditionModel;
        $this->responseMappingModel = $responseMappingModel;
    }

    /**
     * @param \Unific\Extension\Api\Data\WebhookInterface $webhook
     * @return bool
     */
    public function removeWebhook(\Unific\Extension\Api\Data\WebhookInterface $webhook)
    {
        return true;
    }

    /**
     * @param \Unific\Extension\Api\Data\WebhookInterface $webhook
     * @return mixed
     */
    public function getGroupFromWebhook(\Unific\Extension\Api\Data\WebhookInterface $webhook)
    {
        $groupCollection = $this->_objectManager->create('Unific\Extension\Model\Group')->getCollection();
        $groupCollection->addFieldToFilter('name', $webhook->getGroup());

        if ($groupCollection->getSize() <= 0) {
            $group = $this->_objectManager->create('Unific\Extension\Model\Group');
            $group->setName($webhook->getGroup());
            $group->save();
        } else {
            $group = $groupCollection->getFirstItem();
        }

        return $group;
    }

    /**
     * @param \Unific\Extension\Api\Data\WebhookInterface $webhook
     */
    public function saveWebhook(\Unific\Extension\Api\Data\WebhookInterface $webhook)
    {
        $requestCollection = $this->_objectManager->create('Unific\Extension\Model\Request')->getCollection();
        $requestCollection->addFieldToFilter('unique_id', $webhook->getUniqueId());

        if($requestCollection->getSize() <= 0)
        {
            $requestModel = $this->_objectManager->create('Unific\Extension\Model\Request');
        } else {
            $requestModel = $requestCollection->getFirstItem();

            // Remove old mappings
            $mappingCollection = $this->_objectManager->create('Unific\Extension\Model\Mapping')->getCollection();
            $mappingCollection->addFieldToFilter('request_id', $requestModel->getId());
            $mappingCollection->walk('delete');

            // Remove old conditions
            $conditionCollection = $this->_objectManager->create('Unific\Extension\Model\ResourceModel\Condition\Collection');
            $conditionCollection->addFieldToFilter('request_id', $requestModel->getId());
            foreach($conditionCollection->getItems() as $item)
            {
                $item->delete();
            }
            // Remove old response mappings
            $responseMappingCollection = $this->_objectManager->create('Unific\Extension\Model\ResourceModel\ResponseMapping\Collection');
            $responseMappingCollection->addFieldToFilter('request_id', $requestModel->getId());
            foreach($responseMappingCollection->getItems() as $item)
            {
                $item->delete();
            }
            // Remove old response conditions
            $responseConditionCollection = $this->_objectManager->create('Unific\Extension\Model\ResourceModel\ResponseCondition\Collection');
            $responseConditionCollection->addFieldToFilter('request_id', $requestModel->getId());
            foreach($responseConditionCollection->getItems() as $item)
            {
                $item->delete();
            }
        }

        $group = $this->getGroupFromWebhook($webhook);

        // Persist the webhook itself
        $requestModel->setName($webhook->getName());
        $requestModel->setGroupId($group->getId());
        $requestModel->setDescription($webhook->getDescription());
        $requestModel->setRequestEvent($webhook->getEvent());
        $requestModel->setRequestEventExecution($webhook->getEventExecution());
        $requestModel->save();

        // Persist the request mappings if there are any additional set
        if(count($webhook->getMappings()) > 0)
        {
            foreach($webhook->getMappings() as $mapping)
            {
                $typeModel = $this->_objectManager->create('Unific\Extension\Model\Mapping');
                $typeModel->setRequestId($requestModel->getId());
                $typeModel->setInternal($mapping->getInternal());
                $typeModel->getExternal($mapping->getExternal());
                $typeModel->save();
            }
        }

        // Persist the request conditions
        if(count($webhook->getConditions()) > 0)
        {
            foreach($webhook->getConditions() as $count => $condition)
            {
                $conditionModel = $this->_objectManager->create('Unific\Extension\Model\Condition');
                $conditionModel->setRequestId($requestModel->getId());
                $conditionModel->setConditionOrder($count);
                $conditionModel->setCondition($condition->getCondition());
                $conditionModel->setConditionComparison($condition->getComparison());
                $conditionModel->setConditionValue($condition->getValue());
                $conditionModel->setConditionAction($condition->getAction());
                $conditionModel->setConditionActionParams(json_encode($condition->getRequest()->toArray()));
                $conditionModel->save();
            }
        }

        // Persist the response mappings if there are any set
        if(count($webhook->getResponse()->getMappings()) > 0)
        {
            foreach($webhook->getResponse()->getMappings() as $mapping)
            {
                $typeModel = $this->_objectManager->create('Unific\Extension\Model\ResponseMapping');
                $typeModel->setRequestId($requestModel->getId());
                $typeModel->setInternal($mapping->getInternal());
                $typeModel->getExternal($mapping->getExternal());
                $typeModel->save();
            }
        }

        // Persist the response conditions
        if(count($webhook->getResponse()->getConditions()) > 0)
        {
            foreach($webhook->getResponse()->getConditions() as $count => $condition)
            {
                $conditionModel = $this->_objectManager->create('Unific\Extension\Model\ResponseCondition');
                $conditionModel->setRequestId($requestModel->getId());
                $conditionModel->setConditionOrder($count);
                $conditionModel->setCondition($condition->getCondition());
                $conditionModel->setConditionComparison($condition->getComparison());
                $conditionModel->setConditionValue($condition->getValue());
                $conditionModel->setConditionAction($condition->getAction());
                $conditionModel->setConditionActionParams(json_encode($condition->getRequest()->toArray()));
                $conditionModel->save();
            }
        }
    }

    /**
     * @return bool
     */
    public function truncateAllWebhooks()
    {
        return true;
    }

    /**
     * @param $config_path
     * @return mixed
     */
    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
