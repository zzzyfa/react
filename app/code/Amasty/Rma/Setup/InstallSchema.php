<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Setup;

use Amasty\Rma\Model\Request;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'amasty_amrma_status'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amasty_amrma_status'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                'is_active',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false]
            )
            ->addColumn(
                'priority',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true]
            )
            ->addColumn(
                'allow_print_label',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'unsigned' => true]
            )
            ->addColumn(
                'status_key',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )
            ->addIndex(
                $installer->getIdxName('amasty_amrma_status', ['status_key']),
                ['status_key']
            )
            ->setComment('Amasty RMA Status Table');
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'amasty_amrma_status_label'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amasty_amrma_status_label'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                'status_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false]
            )
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'unsigned' => true]
            )
            ->addColumn(
                'label',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )
            ->addIndex(
                $installer->getIdxName(
                    'amasty_amrma_status_label',
                    ['status_id', 'store_id'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['status_id', 'store_id'],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->addForeignKey(
                $installer->getFkName(
                    'amasty_amrma_status_label',
                    'status_id',
                    'amasty_amrma_status',
                    'id'
                ),
                'status_id',
                $installer->getTable('amasty_amrma_status'),
                'id',
                Table::ACTION_CASCADE
            )
            ->setComment('Amasty RMA Status Label Table');
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'amasty_amrma_status_template'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amasty_amrma_status_template'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                'status_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false]
            )
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'unsigned' => true]
            )
            ->addColumn(
                'template',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false]
            )
            ->addIndex(
                $installer->getIdxName(
                    'amasty_amrma_status_label',
                    ['status_id', 'store_id'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['status_id', 'store_id'],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->addForeignKey(
                $installer->getFkName(
                    'amasty_amrma_status_template',
                    'status_id',
                    'amasty_amrma_status',
                    'id'
                ),
                'status_id',
                $installer->getTable('amasty_amrma_status'),
                'id',
                Table::ACTION_CASCADE
            )
            ->setComment('Amasty RMA Status Template Table');
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'amasty_amrma_request'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amasty_amrma_request'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => 0]
            )
            ->addColumn(
                'order_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => true, 'unsigned' => true]
            )
            ->addColumn(
                'increment_id',
                Table::TYPE_TEXT,
                50,
                ['nullable' => false]
            )
            ->addColumn(
                'email',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )
            ->addColumn(
                'customer_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => true, 'unsigned' => true]
            )
            ->addColumn(
                'customer_firstname',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )
            ->addColumn(
                'customer_lastname',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )
            ->addColumn(
                'status_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true]
            )
            ->addColumn(
                'code',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )
            ->addColumn(
                'notes',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false]
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT]
            )
            ->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE]
            )
            ->addColumn(
                'is_shipped',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false]
            )
            ->addIndex(
                $installer->getIdxName(
                    'amasty_amrma_request',
                    ['increment_id']
                ),
                ['increment_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'amasty_amrma_request',
                    'status_id',
                    'amasty_amrma_status',
                    'id'
                ),
                'status_id',
                $installer->getTable('amasty_amrma_status'),
                'id',
                Table::ACTION_RESTRICT
            )
            ->addForeignKey(
                $installer->getFkName(
                    'amasty_amrma_request',
                    'customer_id',
                    'customer_entity',
                    'entity_id'
                ),
                'customer_id',
                $installer->getTable('customer_entity'),
                'entity_id',
                Table::ACTION_SET_NULL
            )
            ->addForeignKey(
                $installer->getFkName(
                    'amasty_amrma_request',
                    'order_id',
                    'sales_order',
                    'entity_id'
                ),
                'order_id',
                $installer->getTable('sales_order'),
                'entity_id',
                Table::ACTION_SET_NULL
            )
            ->addForeignKey(
                $installer->getFkName(
                    'amasty_amrma_request',
                    'store_id',
                    'store',
                    'store_id'
                ),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                Table::ACTION_SET_DEFAULT
            )

            ->setComment('Amasty RMA Status Request Table');

        for ($i = 1; $i <= Request::EXTRA_FIELDS_COUNT; $i++) {
            $table->addColumn(
                "field_$i",
                Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            );
        }

        $installer->getConnection()->createTable($table);


        /**
         * Create table 'amasty_amrma_comment'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amasty_amrma_comment'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                'request_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true]
            )
            ->addColumn(
                'value',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false]
            )
            ->addColumn(
                'is_admin',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false]
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT]
            )
            ->addColumn(
                'unique_key',
                Table::TYPE_TEXT,
                127,
                ['nullable' => false]
            )
            ->addIndex(
                $installer->getIdxName(
                    'amasty_amrma_comment',
                    ['unique_key'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['unique_key'],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->addForeignKey(
                $installer->getFkName(
                    'amasty_amrma_comment',
                    'request_id',
                    'amasty_amrma_request',
                    'id'
                ),
                'request_id',
                $installer->getTable('amasty_amrma_request'),
                'id',
                Table::ACTION_CASCADE
            )
            ->setComment('Amasty RMA Comment Table');
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'amasty_amrma_comment_file'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amasty_amrma_comment_file'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                'comment_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true]
            )
            ->addColumn(
                'file',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )
            ->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )
            ->addForeignKey(
                $installer->getFkName(
                    'amasty_amrma_comment_file',
                    'comment_id',
                    'amasty_amrma_comment',
                    'id'
                ),
                'comment_id',
                $installer->getTable('amasty_amrma_comment'),
                'id',
                Table::ACTION_CASCADE
            )
            ->setComment('Amasty RMA Comment Attachment Table');
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'amasty_amrma_item'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amasty_amrma_item'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                'request_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true]
            )
            ->addColumn(
                'order_item_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true]
            )
            ->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true]
            )
            ->addColumn(
                'sku',
                Table::TYPE_TEXT,
                64,
                ['nullable' => false]
            )
            ->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )
            ->addColumn(
                'qty',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true]
            )
            ->addColumn(
                'reason',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )
            ->addColumn(
                'condition',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )
            ->addColumn(
                'resolution',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )
            ->addForeignKey(
                $installer->getFkName(
                    'amasty_amrma_item',
                    'request_id',
                    'amasty_amrma_request',
                    'id'
                ),
                'request_id',
                $installer->getTable('amasty_amrma_request'),
                'id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    'amasty_amrma_item',
                    'product_id',
                    'catalog_product_entity',
                    'entity_id'
                ),
                'product_id',
                $installer->getTable('catalog_product_entity'),
                'entity_id',
                Table::ACTION_NO_ACTION
            )
            ->addForeignKey(
                $installer->getFkName(
                    'amasty_amrma_item',
                    'order_item_id',
                    'sales_order_item',
                    'item_id'
                ),
                'order_item_id',
                $installer->getTable('sales_order_item'),
                'item_id',
                Table::ACTION_SET_DEFAULT
            )

            ->setComment('Amasty RMA Item Table');
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
