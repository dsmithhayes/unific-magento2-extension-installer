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
         * Create the table for the grouping of requests/responses
         */
        $table = $installer->getConnection()->newTable($installer->getTable('unific_extension_message_queue'))
            ->addColumn('message', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable' => true,
            ), 'Message')
            ->addColumn('retry_amount', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 0, array(
                'unsigned' => true,
                'nullable' => false,
            ), 'Group ID')
            ->addColumn('max_retry_amount', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 0, array(
                'unsigned' => true,
                'nullable' => false,
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
            ->setComment(
                'Message Queue'
            );

        $installer->getConnection()->createTable($table);

        /**
         * Create the table for the grouping of requests/responses
         */
        $table = $installer->getConnection()->newTable($installer->getTable('unific_extension_audit_log'))
            ->addColumn('log_action_type', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable' => true,
            ), 'The type of action performed, this can be a request, response or reconfiguration')
            ->addColumn('log_action_type_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 0, array(
                'unsigned' => true,
                'nullable' => true,
            ), 'Request or Response ID of the action that caused this audit log to be written')
            ->addColumn('log_action_parameters', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable' => true,
            ), 'The parameters of the action performed')
            ->addColumn('log_action_origin', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable' => true,
            ), 'The origin of the action')
            ->addColumn('log_action_user', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable' => true,
            ), 'The user of the action')
            ->addColumn('log_action_user_ip', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable' => true,
            ), 'The user ip of the action')
            ->addColumn('log_date', \Magento\Framework\DB\Ddl\Table::TYPE_DATE, 0, array(
                'nullable' => true,
            ), 'Date when this log was made')
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
         * Create the table for the identity providers
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
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('unific_extension_request_mapping'))
            ->addColumn('id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
            ), 'ID')
            ->addColumn('request_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 0, array(
                'unsigned' => true,
                'nullable' => false,
            ), 'Request ID')
            ->addColumn('location', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable'  => false,
            ), 'Mapping Location')
            ->addColumn('internal', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable'  => false,
            ), 'Internal Attribute')
            ->addColumn('external', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable'  => false,
            ), 'External Attribute')
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
         * Create the request mapping table
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('unific_extension_request_condition'))
            ->addColumn('id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
            ), 'ID')
            ->addColumn('request_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 0, array(
                'unsigned' => true,
                'nullable' => false,
            ), 'Request ID')
            ->addColumn('condition_order', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 0, array(
                'nullable'  => false,
            ), 'Provided condition order')
            ->addColumn('condition', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable'  => false,
            ), 'Provided condition')
            ->addColumn('condition_comparison', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable'  => false,
            ), 'Provided condition comparison')
            ->addColumn('condition_value', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable'  => true,
            ), 'Provided value')
            ->addColumn('condition_action', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable'  => false,
            ), 'Action of condition')
            ->addColumn('condition_action_params', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable'  => true,
            ), 'Action Parameters')
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
         * Create the request mapping table
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('unific_extension_response_mapping'))
            ->addColumn('id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
            ), 'ID')
            ->addColumn('request_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 0, array(
                'unsigned' => true,
                'nullable' => false,
            ), 'Request ID')
            ->addColumn('location', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable'  => false,
            ), 'Mapping Location')
            ->addColumn('internal', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable'  => false,
            ), 'Internal Attribute')
            ->addColumn('external', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable'  => false,
            ), 'External Attribute')
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
         * Create the request mapping table
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('unific_extension_response_condition'))
            ->addColumn('id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
            ), 'ID')
            ->addColumn('request_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 0, array(
                'unsigned' => true,
                'nullable' => false,
            ), 'Request ID')
            ->addColumn('condition_order', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 0, array(
                'nullable'  => false,
            ), 'Provided condition order')
            ->addColumn('condition', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable'  => false,
            ), 'Provided condition')
            ->addColumn('condition_comparison', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable'  => false,
            ), 'Provided condition comparison')
            ->addColumn('condition_value', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable'  => true,
            ), 'Provided value')
            ->addColumn('condition_action', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable'  => false,
            ), 'Action of condition')
            ->addColumn('condition_action_params', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, array(
                'nullable'  => true,
            ), 'Action Parameters')
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