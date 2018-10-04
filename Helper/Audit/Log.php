<?php

namespace Unific\Extension\Helper\Audit;

use Magento\Framework\Exception\InputException;
use Symfony\Component\Config\Definition\Exception\Exception;

class Log extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $objectManager;

    const LOG_SUCCESS = 0;
    const LOG_ERROR_FAILED_TO_RECEIVE = 1;
    const LOG_ERROR_FAILED_TO_SEND = 2;
    const LOG_ERROR_TIMEOUT = 3;
    const LOG_ERROR_UNKNOWN = 4;

    protected $guidHelper;

    /**
     * Log constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Unific\Extension\Helper\Guid $guidHelper
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Unific\Extension\Helper\Guid $guidHelper)
    {
        parent::__construct($context);

        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $this->guidHelper = $guidHelper;
    }

    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Log a message to the database table
     *
     * @param $payload
     * @param array $usedParameters
     * @param int $errorType
     */
    public function log($payload, array $usedParameters, $errorType = Log::LOG_ERROR_FAILED_TO_RECEIVE)
    {
        $logModel = $this->objectManager->create('Unific\Extension\Model\Audit\Log');
        $logModel->setLogGuid($this->guidHelper->generateGuid());
        $logModel->setLogErrorType($errorType);
        $logModel->setActionPayload($payload);
        $logModel->setActionParameters(json_encode($usedParameters));
        $logModel->save();
    }
}
