<?php
namespace Unific\Extension\Helper\Message;

use Magento\Framework\Exception\InputException;
use Symfony\Component\Config\Definition\Exception\Exception;

class Queue extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $objectManager;

    public function __construct(\Magento\Framework\App\Helper\Context $context)
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

    public function queueMessage(array $data, $requestType = "POST")
    {
        $messageModel = $this->objectManager->create('Unific\Extension\Model\Message\Queue');
        $messageModel->addData('message', json_encode($data));
        $messageModel->addData('request_type', $requestType);

        $messageModel->addData('retry_amount',0);
        $messageModel->addData('max_retry_amount', 20);
        $messageModel->addData('message', json_encode($data));
        $messageModel->addData('message', json_encode($data));
    }
}
