<?php

namespace Unific\Extension\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements InstallDataInterface
{
    protected $unificHelper;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(\Unific\Extension\Helper\Data $unificHelper)
    {
        $this->unificHelper = $unificHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->unificHelper->createApiUser();

        $setup->endSetup();
    }
}