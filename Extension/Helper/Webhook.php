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
     * @param \Unific\Extension\Api\Data\WebhookInterface $webhookData
     * @return bool
     */
    public function createWebhook(\Unific\Extension\Api\Data\WebhookInterface $webhookData)
    {
        return true;
    }

    /**
     * @param \Unific\Extension\Api\Data\WebhookInterface $webhookData
     * @return bool
     */
    public function updateWebhook(\Unific\Extension\Api\Data\WebhookInterface $webhookData)
    {
        return true;
    }

    /**
     * @param \Unific\Extension\Api\Data\WebhookInterface $identifier
     * @return bool
     */
    public function removeWebhook(\Unific\Extension\Api\Data\WebhookInterface $identifier)
    {
        return true;
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
