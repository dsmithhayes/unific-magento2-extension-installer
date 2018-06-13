<?php
namespace Unific\Extension\Helper;

use Magento\Framework\Exception\InputException;
use Symfony\Component\Config\Definition\Exception\Exception;

class Request extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $objectManager;

    protected $httpRequest;
    protected $httpClient;
    protected $httpHeaders;

    /**
     * Request constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Zend\Http\Request $httpRequest
     * @param \Zend\Http\Client $httpClient
     * @param \Zend\Http\Headers $httpHeaders
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Zend\Http\Request $httpRequest,
        \Zend\Http\Client $httpClient,
        \Zend\Http\Headers $httpHeaders)
    {
        parent::__construct($context);

        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->httpClient = $httpClient;
        $this->httpRequest = $httpRequest;
        $this->httpHeaders = $httpHeaders;

        $this->httpHeaders->addHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ]);

        $options = [
            'adapter'   => 'Zend\Http\Client\Adapter\Curl',
            'curloptions' => [CURLOPT_FOLLOWLOCATION => true],
            'maxredirects' => 0,
            'timeout' => 30
        ];
        $this->httpClient->setOptions($options);
    }

    /**
     * This sends the actual message via a REST request to Unific
     *
     * @param \Unific\Extension\Model\Message\Queue $messageModel
     * @param $url
     * @param string $requestType
     * @return string Result message
     */
    public function sendMessage(\Unific\Extension\Model\Message\Queue $messageModel, $url, $requestType = \Zend\Http\Request::METHOD_POST)
    {
        $this->httpRequest->setHeaders($this->httpHeaders);
        $this->httpRequest->setUri($url);
        $this->httpRequest->setMethod(\Zend\Http\Request::METHOD_GET);

        $params = new \Zend\Stdlib\Parameters($messageModel->getData());
        $this->httpRequest->setQuery($params);

        return $this->httpClient->send($this->httpRequest);
    }

    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
