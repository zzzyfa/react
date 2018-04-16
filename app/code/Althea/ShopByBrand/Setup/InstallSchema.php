<?php
/**
 * Created by PhpStorm.
 * User: manadirmahi
 * Date: 10/10/2017
 * Time: 10:20 AM
 */

namespace Althea\ShopByBrand\Setup;

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
		 * Create table 'tm_brand_followers'
		 */
		$table = $installer->getConnection()
		                   ->newTable($installer->getTable('tm_brand_followers'))
		                   ->addColumn('follower_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, [
			                   'identity' => true,
			                   'nullable' => false,
			                   'primary'  => true,
		                   ], 'FollowerRepository ID')
		                   ->addColumn('customer_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
			                   'nullable' => false,
			                   'unsigned' => true,
		                   ], 'Customer ID')
		                   ->addColumn('brand_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
			                   'nullable' => false,
			                   'unsigned' => true,
		                   ], 'Follower ID')
		                   ->addColumn('is_active', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, [
			                   'nullable' => false,
			                   'default'  => '0',
		                   ], 'Is FollowerRepository Active')
		                   ->addColumn('created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [
			                   'nullable' => false,
			                   'default'  => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
		                   ], 'FollowerRepository Created At')
		                   ->addColumn('updated_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [
			                   'nullable' => false,
			                   'default'  => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE,
		                   ], 'FollowerRepository Updated At')
		                   ->addIndex(
			                   $setup->getIdxName(
				                   $installer->getTable('tm_brand_followers'),
				                   ['follower_id'],
				                   AdapterInterface::INDEX_TYPE_INDEX
			                   ),
			                   ['follower_id'],
			                   ['type' => AdapterInterface::INDEX_TYPE_INDEX]
		                   )
		                   ->addForeignKey(
			                   $installer->getFkName(
				                   'tm_brand_followers',
				                   'customer_id',
				                   'customer_entity',
				                   'entity_id'
			                   ),
			                   'customer_id',
			                   $installer->getTable('customer_entity'),
			                   'entity_id',
			                   \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
		                   )
		                   ->addForeignKey(
			                   $installer->getFkName(
				                   'tm_brand_followers',
				                   'brand_id',
				                   'tm_brand',
				                   'brand_id'
			                   ),
			                   'brand_id',
			                   $installer->getTable('tm_brand'),
			                   'brand_id',
			                   \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
		                   )
		                   ->setComment('Althea Follower FollowerRepository Table');
		$installer->getConnection()->createTable($table);
		$installer->endSetup();

	}
}