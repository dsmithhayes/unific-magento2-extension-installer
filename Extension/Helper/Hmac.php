<?php
namespace Unific\Extension\Helper;

use Magento\Framework\Exception\InputException;
use Symfony\Component\Config\Definition\Exception\Exception;

class Hmac extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $scopeConfig;

    /**
     * Request constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        parent::__construct($context);

        $this->scopeConfig = $scopeConfig;
    }

    public function generateHmac(array $data)
    {
        return hash_hmac(
            $this->scopeConfig->getValue('unific/extension/hashAlgorithm', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
            serialize($data),
            $this->scopeConfig->getValue('unific/extension/hmacSecret', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
        );
    }

    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
