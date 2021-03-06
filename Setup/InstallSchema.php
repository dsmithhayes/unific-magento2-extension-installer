<?php

namespace Unific\Extension\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup,
                            ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /**
         * The MySQL Table to implement the AMQP behaviour
         */
        $table = $installer->getConnection()->newTable($installer->getTable('unific_extension_message_queue'))
            ->addColumn('guid', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable' => true,
            ), 'A unique GUID identifier')
            ->addColumn('message', \Magento\Framework\DB\Ddl\Table::TYPE_LONGTEXT, 0, array(
                'nullable' => true,
            ), 'Message')
            ->addColumn('request_type', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable' => false,
            ), 'The request type, can be POST, PUT, DELETE')
            ->addColumn('retry_amount', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 0, array(
                'unsigned' => true,
                'nullable' => false,
                'default' => 0
            ), 'Group ID')
            ->addColumn('max_retry_amount', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 0, array(
                'unsigned' => true,
                'nullable' => false,
                'default' => 20
            ), 'Group ID')
            ->addColumn('response_error', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable' => true,
            ), 'Message')
            ->addColumn('response_http_code', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 0, array(
                'unsigned' => true,
                'nullable' => false,
            ), 'Group ID')
            ->addColumn('request_date_first', \Magento\Framework\DB\Ddl\Table::TYPE_DATE, 0, array(
                'nullable' => true,
            ), 'Date where this request was first sent')
            ->addColumn('request_date_last', \Magento\Framework\DB\Ddl\Table::TYPE_DATE, 0, array(
                'nullable' => true,
            ), 'Date where this request was last sent')
            ->addColumn(
                'request_date_first',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Date where this request was first sent')
            ->addColumn(
                'request_date_last',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Date where this request was last sent')
            ->setComment(
                'Message Queue'
            );

        $installer->getConnection()->createTable($table);

        /**
         * The audit log for just about anything that the extension does
         */
        $table = $installer->getConnection()->newTable($installer->getTable('unific_extension_audit_log'))
            ->addColumn('id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true,
            ), 'ID')
            ->addColumn('log_guid', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable' => true,
            ), 'A unique GUID identifier')
            ->addColumn('log_error_type', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 0, array(
                'unsigned' => true,
                'nullable' => false,
            ), 'The type of the error, constants are in the log helper')
            ->addColumn('log_action_parameters', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable' => true,
            ), 'The type of action performed, this can be a request, response or reconfiguration')
            ->addColumn('log_action_payload', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable' => true,
            ), 'The payload of the action performed')
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Creation date')
            ->addColumn(
                'updated_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Update date')
            ->setComment(
                'Unific Audit Log'
            );

        $installer->getConnection()->createTable($table);

        /**
         * Create the table for the grouping of requests/responses
         */
        $table = $installer->getConnection()->newTable($installer->getTable('unific_extension_group'))
            ->addColumn('id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true,
            ), 'ID')
            ->addColumn('name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable' => false,
            ), 'Name')
            ->addColumn('description', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable' => true,
            ), 'Description')
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Creation date')
            ->addColumn(
                'updated_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Update date')
            ->addIndex(
                $setup->getIdxName(
                    $installer->getTable('unific_extension_group'),
                    ['name'],
                    AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['name'],
                ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
            )->setComment(
                'Extension Group'
            );

        $installer->getConnection()->createTable($table);

        /**
         * Create the table for the requests that should be sent out
         * This is the actual request to the Unific platform
         */
        $table = $installer->getConnection()->newTable($installer->getTable('unific_extension_request'))
            ->addColumn('id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true,
            ), 'ID')
            ->addColumn('name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable' => false,
            ), 'Name')
            ->addColumn('group_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 0, array(
                'unsigned' => true,
                'nullable' => false,
            ), 'Group ID')
            ->addColumn('description', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable' => true,
            ), 'Description')
            ->addColumn('request_event', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable' => false,
            ), 'Called Event')
            ->addColumn('request_event_execution', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable' => false,
            ), 'Event Execution Time')
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Creation date')
            ->addColumn(
                'updated_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Update date')
            ->addIndex(
                $setup->getIdxName(
                    $installer->getTable('unific_extension_request'),
                    ['name'],
                    AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['name'],
                ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
            )
            ->addIndex(
                $installer->getIdxName('unific_extension_request', ['group_id']),
                ['group_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'unific_extension_request',
                    'group_id',
                    'unific_extension_group',
                    'id'
                ),
                'group_id',
                $installer->getTable('unific_extension_group'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment(
                'Extension Requests'
            );

        $installer->getConnection()->createTable($table);


        /**
         * Create the request mapping table
         * These mappings will map any magento internals to any unific known fields
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('unific_extension_request_mapping'))
            ->addColumn('id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true,
            ), 'ID')
            ->addColumn('request_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 0, array(
                'unsigned' => true,
                'nullable' => false,
            ), 'Request ID')
            ->addColumn('location', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable' => false,
            ), 'Mapping Location')
            ->addColumn('internal', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable' => false,
            ), 'Internal Attribute')
            ->addColumn('external', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable' => false,
            ), 'External Attribute')
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Creation date')
            ->addColumn(
                'updated_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Update date')
            ->addIndex(
                $installer->getIdxName('unific_extension_request_mapping', ['request_id']),
                ['request_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'unific_extension_request_mapping',
                    'request_id',
                    'unific_extension_request',
                    'id'
                ),
                'request_id',
                $installer->getTable('unific_extension_request'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment(
                'Request Mapping Data'
            );
        $installer->getConnection()->createTable($table);

        /**
         * Create the request conditions
         * These conditions apply to determine if a request has to be actually made
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('unific_extension_request_condition'))
            ->addColumn('id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true,
            ), 'ID')
            ->addColumn('request_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 0, array(
                'unsigned' => true,
                'nullable' => false,
            ), 'Request ID')
            ->addColumn('condition_order', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 0, array(
                'nullable' => false,
            ), 'Provided condition order')
            ->addColumn('condition', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable' => false,
            ), 'Provided condition')
            ->addColumn('condition_comparison', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable' => false,
            ), 'Provided condition comparison')
            ->addColumn('condition_value', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable' => true,
            ), 'Provided value')
            ->addColumn('condition_action', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable' => false,
            ), 'Action of condition')
            ->addColumn('condition_action_params', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable' => true,
            ), 'Action Parameters')
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Creation date')
            ->addColumn(
                'updated_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Update date')
            ->addIndex(
                $installer->getIdxName('unific_extension_request_condition', ['request_id']),
                ['request_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'unific_extension_request_condition',
                    'request_id',
                    'unific_extension_request',
                    'id'
                ),
                'request_id',
                $installer->getTable('unific_extension_request'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment(
                'Request Condition Data'
            );
        $installer->getConnection()->createTable($table);

        /**
         * Create the response mapping table
         * When Unific sends a response, if it contains fields to be processed, we map them to magento fields here
         * This could be that data is "enriched" with data from Unific
         * Like when a new customer is created, and may get a special Unific identifier
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('unific_extension_response_mapping'))
            ->addColumn('id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true,
            ), 'ID')
            ->addColumn('request_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 0, array(
                'unsigned' => true,
                'nullable' => false,
            ), 'Request ID')
            ->addColumn('location', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable' => false,
            ), 'Mapping Location')
            ->addColumn('internal', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable' => false,
            ), 'Internal Attribute')
            ->addColumn('external', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable' => false,
            ), 'External Attribute')
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Creation date')
            ->addColumn(
                'updated_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Update date')
            ->addIndex(
                $installer->getIdxName('unific_extension_response_mapping', ['request_id']),
                ['request_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'unific_extension_response_mapping',
                    'request_id',
                    'unific_extension_request',
                    'id'
                ),
                'request_id',
                $installer->getTable('unific_extension_request'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment(
                'Response Mapping Data'
            );
        $installer->getConnection()->createTable($table);

        /**
         * Create the response conditions
         * These conditions determine if we're going to do any further processing with the response from Unific
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('unific_extension_response_condition'))
            ->addColumn('id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true,
            ), 'ID')
            ->addColumn('request_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 0, array(
                'unsigned' => true,
                'nullable' => false,
            ), 'Request ID')
            ->addColumn('condition_order', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 0, array(
                'nullable' => false,
            ), 'Provided condition order')
            ->addColumn('condition', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable' => false,
            ), 'Provided condition')
            ->addColumn('condition_comparison', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable' => false,
            ), 'Provided condition comparison')
            ->addColumn('condition_value', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable' => true,
            ), 'Provided value')
            ->addColumn('condition_action', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable' => false,
            ), 'Action of condition')
            ->addColumn('condition_action_params', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable' => true,
            ), 'Action Parameters')
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Creation date')
            ->addColumn(
                'updated_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Update date')
            ->addIndex(
                $installer->getIdxName('unific_extension_response_condition', ['request_id']),
                ['request_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'unific_extension_response_condition',
                    'request_id',
                    'unific_extension_request',
                    'id'
                ),
                'request_id',
                $installer->getTable('unific_extension_request'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment(
                'Response Condition Data'
            );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}