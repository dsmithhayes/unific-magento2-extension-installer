<?php
namespace Unific\Extension\Helper;

use Magento\Framework\Exception\InputException;
use Symfony\Component\Config\Definition\Exception\Exception;

class Request extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $objectManager;

    protected $scopeConfig;
    protected $httpRequest;
    protected $httpClient;
    protected $httpHeaders;
    protected $guidHelper;
    protected $hmacHelper;

    /**
     * Request constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Zend\Http\Request $httpRequest
     * @param \Zend\Http\Client $httpClient
     * @param \Zend\Http\Headers $httpHeaders
     * @param Hmac $hmacHelper
     * @param Guid $guidHelper
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Zend\Http\Request $httpRequest,
        \Zend\Http\Client $httpClient,
        \Zend\Http\Headers $httpHeaders,
        Hmac $hmacHelper,
        Guid $guidHelper)
    {
        parent::__construct($context);

        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->httpClient = $httpClient;
        $this->httpRequest = $httpRequest;
        $this->httpHeaders = $httpHeaders;
        $this->guidHelper = $guidHelper;
        $this->scopeConfig = $scopeConfig;
        $this->hmacHelper = $hmacHelper;

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
        $this->httpRequest->setMethod($requestType);

        $paramsToSend = $messageModel->getData();
        $paramsToSend["nonce"] = $this->guidHelper->generateGuid();
        $paramsToSend["timestamp"] = time();
        $paramsToSend["hmac"] = $this->hmacHelper($paramsToSend);

        $params = new \Zend\Stdlib\Parameters($paramsToSend);
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
