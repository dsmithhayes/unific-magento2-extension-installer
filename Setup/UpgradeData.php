<?php

namespace Unific\Extension\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class UpgradeData
 * @package Unific\Extension\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Unific\Extension\Helper\Data
     */
    protected $unificHelper;

    /**
     * @var \Unific\Extension\Helper\Hmac
     */
    protected $hmacHelper;

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $configWriter;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Init
     *
     * @param \Unific\Extension\Helper\Data $unificHelper
     * @param \Unific\Extension\Helper\Hmac $hmacHelper
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Unific\Extension\Helper\Data $unificHelper,
        \Unific\Extension\Helper\Hmac $hmacHelper,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->unificHelper = $unificHelper;
        $this->hmacHelper = $hmacHelper;
        $this->configWriter = $configWriter;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->unificHelper->createApiUser();

        if($this->scopeConfig->getValue('unific/hmac/hmacSecret', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) == null ||
            $this->scopeConfig->getValue('unific/hmac/hmacSecret', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) == '')
        {
            // Persist the hmac data
            $this->configWriter->save('unific/hmac/hmacEnable', true);
            $this->configWriter->save('unific/hmac/hmacHeader', 'X-Magento-Unific-Hmac');
            $this->configWriter->save('unific/hmac/hmacAlgorithm', 'sha256');
            $this->configWriter->save('unific/hmac/hmacSecret', $this->hmacHelper->generateSecret());
        }

        $setup->endSetup();
    }
}
