<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 24/07/2017
 * Time: 4:26 PM
 */

namespace Althea\Nexmo\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface {

	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
	{
		$installer = $setup;

		$installer->startSetup();

		$table = $installer->getConnection()
		                   ->newTable($installer->getTable('althea_nexmo_verification'))
		                   ->addColumn('verification_id', Table::TYPE_INTEGER, null, [
			                   'identity' => true,
			                   'unsigned' => true,
			                   'nullable' => false,
			                   'primary'  => true,
		                   ], 'Verification ID')
		                   ->addColumn('customer_id', Table::TYPE_INTEGER, null, [
			                   'unsigned' => true,
			                   'nullable' => false,
		                   ], 'Customer ID')
		                   ->addColumn('website_id', Table::TYPE_SMALLINT, null, [
			                   'unsigned' => true,
			                   'nullable' => true,
		                   ], 'Website ID')
		                   ->addColumn('phone_number', Table::TYPE_TEXT, 50, [
			                   'nullable' => false,
		                   ], 'Customer ID')
		                   ->addColumn('request_id', Table::TYPE_TEXT, 32, [
			                   'nullable' => false,
		                   ], 'Customer ID')
		                   ->addColumn('status', Table::TYPE_INTEGER, null, [
			                   'unsigned' => true,
			                   'nullable' => false,
			                   'default'  => 0,
		                   ], 'Customer ID')
		                   ->addColumn('created_at', Table::TYPE_DATETIME, null, [
			                   'nullable' => false,
		                   ], 'Created At')
		                   ->addColumn('updated_at', Table::TYPE_DATETIME, null, [
			                   'nullable' => false,
		                   ], 'Updated At')
		                   ->addIndex($installer->getIdxName('althea_nexmo_verification', ['website_id']), ['website_id'])
		                   ->addForeignKey(
			                   $installer->getFkName('althea_nexmo_verification', 'customer_id', 'customer_entity', 'entity_id'),
			                   'customer_id',
			                   $installer->getTable('customer_entity'),
			                   'entity_id',
			                   \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
		                   )
		                   ->addForeignKey(
			                   $installer->getFkName('althea_nexmo_verification', 'website_id', 'store_website', 'website_id'),
			                   'website_id',
			                   $installer->getTable('store_website'),
			                   'website_id',
			                   \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
		                   );

		$installer->getConnection()->createTable($table);
		$installer->endSetup();
	}

}