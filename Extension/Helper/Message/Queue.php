<?php
namespace Unific\Extension\Helper\Message;

use Magento\Framework\Exception\InputException;
use Symfony\Component\Config\Definition\Exception\Exception;
use Unific\Extension\Helper\Audit\Log;

class Queue extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $objectManager;

    /*
     * \Unific\Extension\Helper\Audit\Log
     *
     * Write logs to the audit log on failures
     */
    protected $auditLog;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Unific\Extension\Helper\Audit\Log $auditLog)
    {
        parent::__construct($context);

        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->auditLog = $auditLog;
    }

    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function queue(array $data, $requestType = "POST")
    {
        $messageModel = $this->objectManager->create('Unific\Extension\Model\Message\Queue');
        $messageModel->setGuid($this->newGUID());
        $messageModel->setMessage(json_encode($data));
        $messageModel->setRequestType($requestType);
        $messageModel->setRetryAmount(0);
        $messageModel->setMaxRetryAmount(20);
        $messageModel->save();
    }

    /**
     * @param \Unific\Extension\Model\Message\Queue $message
     *
     * Requeue a message if sending failed
     */
    public function requeue(\Unific\Extension\Model\Message\Queue $message)
    {
        if($message->getRetryAmount() < $message->getMaxRetryAmount())
        {
            $message->setRetryAmount($message->getRetryAmount() + 1);
        } else {
            $this->logMessage($message, Log::LOG_ERROR_FAILED_TO_RECEIVE);
            $message->delete();
        }
    }

    /**
     * @param \Unific\Extension\Model\Message\Queue $message
     * @param int $errorType
     *
     * The message failed to send and is written to the audit log
     */
    public function logMessage(\Unific\Extension\Model\Message\Queue $message, $errorType = Log::LOG_SUCCESS)
    {
        $this->auditLog->log(
            $message->getMessage(),
            array(
                'guid' => $message->getGuid(),
                'request_type' => $message->getRequestType(),
                'response_http_code' => $message->getResponseHttpCode(),
                'response_error' => $message->getResponseError(),
                'request_date_first' => $message->getRequestDateFirst(),
                'request_date_last' => $message->getRequestDateLast()
            ),
            $errorType
        );
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
