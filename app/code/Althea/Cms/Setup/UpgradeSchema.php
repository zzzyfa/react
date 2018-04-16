<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 11/08/2017
 * Time: 3:49 PM
 */

namespace Althea\Cms\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface {

	public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
	{
		$setup->startSetup();

		if (version_compare($context->getVersion(), '1.1.0', '<')) {

			$this->_createCmsAlertTables($setup);
		}

		$setup->endSetup();
	}

	protected function _createCmsAlertTables(SchemaSetupInterface $setup)
	{
		/**
		 * Create table 'althea_cms_alert'
		 */
		$table = $setup->getConnection()
		               ->newTable($setup->getTable('althea_cms_alert'))
		               ->addColumn('alert_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, [
			               'identity' => true,
			               'nullable' => false,
			               'primary'  => true,
		               ], 'Alert ID')
		               ->addColumn('title', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
			               'nullable' => false,
		               ], 'Alert Title')
		               ->addColumn('identifier', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
			               'nullable' => false,
		               ], 'Alert String Identifier')
		               ->addColumn('content', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '2M', [], 'Alert Content')
		               ->addColumn('created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [
			               'nullable' => false,
			               'default'  => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
		               ], 'Alert Created At')
		               ->addColumn('updated_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [
			               'nullable' => false,
			               'default'  => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE,
		               ], 'Alert Updated At')
		               ->addColumn('is_active', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, [
			               'nullable' => false,
			               'default'  => '0',
		               ], 'Is Alert Active')
		               ->addIndex(
			               $setup->getIdxName(
				               $setup->getTable('althea_cms_alert'),
				               ['title', 'identifier', 'content'],
				               AdapterInterface::INDEX_TYPE_FULLTEXT
			               ),
			               ['title', 'identifier', 'content'],
			               ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
		               )
		               ->setComment('Althea CMS Alert Table');
		$setup->getConnection()->createTable($table);

		/**
		 * Create table 'althea_cms_alert_store'
		 */
		$table = $setup->getConnection()
		               ->newTable($setup->getTable('althea_cms_alert_store'))
		               ->addColumn('alert_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, [
			               'nullable' => false,
			               'primary'  => true,
		               ], 'Alert ID')
		               ->addColumn('store_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, [
			               'unsigned' => true,
			               'nullable' => false,
			               'primary'  => true,
		               ], 'Store ID')
		               ->addIndex($setup->getIdxName('althea_cms_alert_store', ['store_id']), ['store_id'])
		               ->addForeignKey(
			               $setup->getFkName('althea_cms_alert_store', 'alert_id', 'althea_cms_alert', 'alert_id'),
			               'alert_id',
			               $setup->getTable('althea_cms_alert'),
			               'alert_id',
			               \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
		               )
		               ->addForeignKey(
			               $setup->getFkName('althea_cms_alert_store', 'store_id', 'store', 'store_id'),
			               'store_id',
			               $setup->getTable('store'),
			               'store_id',
			               \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
		               )
		               ->setComment('Althea CMS Alert To Store Linkage Table');
		$setup->getConnection()->createTable($table);
	}

}