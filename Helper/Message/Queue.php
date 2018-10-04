<?php

namespace Unific\Extension\Helper\Message;

use Magento\Framework\Exception\InputException;
use Symfony\Component\Config\Definition\Exception\Exception;

class Queue extends \Magento\Framework\App\Helper\AbstractHelper
{
    const QUEUE_MODE_LIVE = 'live';
    const QUEUE_MODE_BURST = 'burst';

    public $queueMode = \Unific\Extension\Helper\Message\Queue::QUEUE_MODE_LIVE;

    protected $logger;

    /**
     * \Unific\Extension\Helper\Request
     * Will handle the actual requests
     */
    protected $requestHelper;

    protected $scopeConfig;

    protected $guidHelper;

    protected $queueFactory;

    /**
     * Queue constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Unific\Extension\Helper\Request $requestHelper
     * @param \Unific\Extension\Logger\Logger $logger
     * @param \Unific\Extension\Helper\Guid $guidHelper
     * @param \Unific\Extension\Model\Message\QueueFactory $queueFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Unific\Extension\Helper\Request $requestHelper,
        \Unific\Extension\Logger\Logger $logger,
        \Unific\Extension\Helper\Guid $guidHelper,
        \Unific\Extension\Model\Message\QueueFactory $queueFactory)
    {
        parent::__construct($context);

        $this->logger = $logger;
        $this->requestHelper = $requestHelper;
        $this->scopeConfig = $scopeConfig;
        $this->guidHelper = $guidHelper;
        $this->queueFactory = $queueFactory;
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

    /**
     * Queue a message for sending
     *
     * @param $url
     * @param $extraHeaders
     * @param array $data
     * @param $requestType
     * @param bool $historical
     * @param int $responseHttpCode
     * @param int $retryAmount
     * @param int $maxRetryAmount
     * @param null $guid
     * @param null $error
     * @return
     */
    public function queue($url,
                          $extraHeaders,
                          $data,
                          $requestType = \Zend_Http_Client::POST,
                          $historical = false,
                          $responseHttpCode = 200,
                          $retryAmount = 0,
                          $maxRetryAmount = 20,
                          $guid = null,
                          $error = null
    )
    {
        $messageModel = $this->queueFactory->create();

        $messageModel->setData(array(
            'guid' => $guid == null ? $this->guidHelper->generateGuid() : $guid,
            'url' => $url,
            'headers' => json_encode($extraHeaders),
            'message' => json_encode($data),
            'request_type' => $requestType,
            'response_http_code' => $responseHttpCode,
            'retry_amount' => $retryAmount,
            'max_retry_amount' => $maxRetryAmount,
            'historical' => (int) $historical
        ));

        $this->logger->info('Before saving to queue: ', $messageModel->getData());
        $messageModel->save();
        $this->logger->info('After saving to queue: ', $messageModel->getData());

        return $messageModel->getData('guid');
    }

    /**
     * @param \Unific\Extension\Model\Message\Queue $message
     *
     * Requeue a message if sending failed
     */
    public function requeue(\Unific\Extension\Model\Message\Queue $message)
    {
        if ($message->getRetryAmount() < $message->getMaxRetryAmount()) {
            $message->setRetryAmount($message->getRetryAmount() + 1);
            $message->save();
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


}
