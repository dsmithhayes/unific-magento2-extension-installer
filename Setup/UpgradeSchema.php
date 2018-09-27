<?php

namespace Unific\Extension\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements  UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context){

        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.2') < 0) {

            // Get module table
            $tableName = $setup->getTable('unific_extension_request');

            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {

                // Declare data
                $columns = [
                    'unique_id' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => false,
                        'comment' => 'Unique ID for a request',
                    ],
                ];

                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }

            }
        }

        if (version_compare($context->getVersion(), '1.0.3') < 0) {

            // Declare data
            $columns = [
                'webhook' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Webhook to send',
                ],
            ];

            // Get module table
            $tableName = $setup->getTable('unific_extension_request_condition');

            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }

            }

            // Get module table
            $tableName = $setup->getTable('unific_extension_response_condition');

            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }

            }
        }

        $setup->endSetup();
    }
}