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

    public function __construct(
        \Magento\Framework\App\Helper\Context $context)
    {
        parent::__construct($context);

        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function log($payload, array $usedParameters, $errorType = Log::LOG_ERROR_FAILED_TO_RECEIVE)
    {
        $logModel = $this->objectManager->create('Unific\Extension\Model\Audit\Log');
        $logModel->setLogGuid($this->newGUID());
        $logModel->setLogErrorType($errorType);
        $logModel->setActionPayload($payload);
        $logModel->setActionParameters(json_encode($usedParameters));
        $logModel->save();
    }

    function newGUID()
    {
        if (function_exists('com_create_guid') === true)
        {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }
}
