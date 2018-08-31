<?php

namespace Unific\Extension\Helper;

use Magento\Framework\Exception\InputException;
use Symfony\Component\Config\Definition\Exception\Exception;

class Webhook extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Unific\Extension\Model\RequestFactory
     */
    protected $requestFactory;

    /**
     * @var \Unific\Extension\Model\MappingFactory
     */
    protected $mappingFactory;

    /**
     * @var \Unific\Extension\Model\ConditionFactory
     */
    protected $conditionFactory;

    /**
     * @var \Unific\Extension\Model\ResponseConditionFactory
     */
    protected $responseConditionFactory;

    /**
     * @var \Unific\Extension\Model\ResponseMappingFactory
     */
    protected $responseMappingFactory;
    /**
     * @var \Unific\Extension\Model\GroupFactory
     */
    private $groupFactory;

    /**
     * Request constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Unific\Extension\Model\GroupFactory $groupFactory
     * @param \Unific\Extension\Model\RequestFactory $requestFactory
     * @param \Unific\Extension\Model\MappingFactory $mappingFactory
     * @param \Unific\Extension\Model\ConditionFactory $conditionFactory
     * @param \Unific\Extension\Model\ResponseConditionFactory $responseConditionFactory
     * @param \Unific\Extension\Model\ResponseMappingFactory $responseMappingFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Unific\Extension\Model\GroupFactory $groupFactory,
        \Unific\Extension\Model\RequestFactory $requestFactory,
        \Unific\Extension\Model\MappingFactory $mappingFactory,
        \Unific\Extension\Model\ConditionFactory $conditionFactory,
        \Unific\Extension\Model\ResponseConditionFactory $responseConditionFactory,
        \Unific\Extension\Model\ResponseMappingFactory $responseMappingFactory)
    {
        parent::__construct($context);

        $this->scopeConfig = $scopeConfig;
        $this->requestFactory = $requestFactory;
        $this->mappingFactory = $mappingFactory;
        $this->conditionFactory = $conditionFactory;
        $this->responseConditionFactory = $responseConditionFactory;
        $this->responseMappingFactory = $responseMappingFactory;
        $this->groupFactory = $groupFactory;
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
        $groupCollection = $this->groupFactory->create()->getCollection();
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
        $requestCollection = $this->requestFactory->create()->getCollection();
        $requestCollection->addFieldToFilter('unique_id', $webhook->getUniqueId());

        if($requestCollection->getSize() <= 0)
        {
            $requestModel = $this->_objectManager->create('Unific\Extension\Model\Request');
        } else {
            $requestModel = $requestCollection->getFirstItem();

            // Remove old mappings
            $mappingCollection = $this->mappingFactory->create()->getCollection();
            $mappingCollection->addFieldToFilter('request_id', $requestModel->getId());
            $mappingCollection->walk('delete');

            // Remove old conditions
            $conditionCollection = $this->conditionFactory->create()->getCollection();
            $conditionCollection->addFieldToFilter('request_id', $requestModel->getId());
            foreach($conditionCollection->getItems() as $item)
            {
                $item->delete();
            }
            // Remove old response mappings
            $responseMappingCollection = $this->responseMappingFactory->create()->getCollection();
            $responseMappingCollection->addFieldToFilter('request_id', $requestModel->getId());
            foreach($responseMappingCollection->getItems() as $item)
            {
                $item->delete();
            }
            // Remove old response conditions
            $responseConditionCollection = $this->responseConditionFactory->create()->getCollection();
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
                $typeModel = $this->mappingFactory->create();
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
                $conditionModel = $this->conditionFactory->create();
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
                $typeModel = $this->responseMappingFactory->create();
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
                $conditionModel = $this->responseConditionFactory->create();;
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
