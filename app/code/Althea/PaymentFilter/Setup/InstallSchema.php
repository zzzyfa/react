<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 07/09/2017
 * Time: 10:57 AM
 */

namespace Althea\PaymentFilter\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface {

	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
	{
		$installer = $setup;

		$installer->startSetup();

		/**
		 * Create table 'althea_paymentfilter_rule'
		 */
		$table = $installer->getConnection()
		                   ->newTable($installer->getTable('althea_paymentfilter_rule'))
		                   ->addColumn('rule_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, [
			                   'identity' => true,
			                   'nullable' => false,
			                   'primary'  => true,
		                   ], 'Rule ID')
		                   ->addColumn('name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
			                   'nullable' => false,
		                   ], 'Rule Name')
		                   ->addColumn('status', \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN, null, [
			                   'default' => 0,
		                   ], 'Rule Status')
		                   ->addColumn('conditions_serialized', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '2M', [], 'Rule Conditions')
		                   ->addColumn('payment_method', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 512, [
			                   'nullable' => false,
		                   ], 'Rule Payment Method')
		                   ->addColumn('shipping_method', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 512, [], 'Rule Shipping Method')
		                   ->addColumn('priority', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 4, [
			                   'default' => 0,
		                   ], 'Rule Priority')
		                   ->addColumn('stop_rules_processing', \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN, null, [
			                   'default' => 0,
		                   ], 'Rule Name')
		                   ->addColumn('created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [
			                   'nullable' => false,
			                   'default'  => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
		                   ], 'Rule Created At')
		                   ->addColumn('updated_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [
			                   'nullable' => false,
			                   'default'  => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE,
		                   ], 'Rule Updated At')
		                   ->addIndex(
			                   $setup->getIdxName(
				                   $installer->getTable('althea_paymentfilter_rule'),
				                   ['name', 'payment_method', 'shipping_method'],
				                   AdapterInterface::INDEX_TYPE_FULLTEXT
			                   ),
			                   ['name', 'payment_method', 'shipping_method'],
			                   ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
		                   )
		                   ->setComment('Althea Payment Filter Table');
		$installer->getConnection()->createTable($table);

		/**
		 * Create table 'althea_paymentfilter_rule_store'
		 */
		$table = $installer->getConnection()
		                   ->newTable($installer->getTable('althea_paymentfilter_rule_store'))
		                   ->addColumn('rule_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, [
			                   'nullable' => false,
			                   'primary'  => true,
		                   ], 'Rule ID')
		                   ->addColumn('store_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, [
			                   'unsigned' => true,
			                   'nullable' => false,
			                   'primary'  => true,
		                   ], 'Store ID')
		                   ->addIndex($installer->getIdxName('althea_paymentfilter_rule_store', ['store_id']), ['store_id'])
		                   ->addForeignKey(
			                   $installer->getFkName('althea_paymentfilter_rule_store', 'rule_id', 'althea_paymentfilter_rule', 'rule_id'),
			                   'rule_id',
			                   $installer->getTable('althea_paymentfilter_rule'),
			                   'rule_id',
			                   \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
		                   )
		                   ->addForeignKey(
			                   $installer->getFkName('althea_paymentfilter_rule_store', 'store_id', 'store', 'store_id'),
			                   'store_id',
			                   $installer->getTable('store'),
			                   'store_id',
			                   \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
		                   )
		                   ->setComment('Althea Payment Filter Rule To Store Linkage Table');
		$installer->getConnection()->createTable($table);

		$installer->endSetup();
	}

}