<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 04/08/2017
 * Time: 12:42 PM
 */

namespace Althea\Cms\Setup;

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
		 * Create table 'althea_cms_banner'
		 */
		$table = $installer->getConnection()
		                   ->newTable($installer->getTable('althea_cms_banner'))
		                   ->addColumn('banner_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, [
			                   'identity' => true,
			                   'nullable' => false,
			                   'primary'  => true,
		                   ], 'Banner ID')
		                   ->addColumn('title', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
			                   'nullable' => false,
		                   ], 'Banner Title')
		                   ->addColumn('identifier', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
			                   'nullable' => false,
		                   ], 'Banner String Identifier')
		                   ->addColumn('content', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '2M', [], 'Banner Content')
		                   ->addColumn('created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [
			                   'nullable' => false,
			                   'default'  => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
		                   ], 'Banner Created At')
		                   ->addColumn('updated_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [
			                   'nullable' => false,
			                   'default'  => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE,
		                   ], 'Banner Updated At')
		                   ->addColumn('is_active', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, [
			                   'nullable' => false,
			                   'default'  => '0',
		                   ], 'Is Banner Active')
		                   ->addIndex(
			                   $setup->getIdxName(
				                   $installer->getTable('althea_cms_banner'),
				                   ['title', 'identifier', 'content'],
				                   AdapterInterface::INDEX_TYPE_FULLTEXT
			                   ),
			                   ['title', 'identifier', 'content'],
			                   ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
		                   )
		                   ->setComment('Althea CMS Banner Table');
		$installer->getConnection()->createTable($table);

		/**
		 * Create table 'althea_cms_banner_store'
		 */
		$table = $installer->getConnection()
		                   ->newTable($installer->getTable('althea_cms_banner_store'))
		                   ->addColumn('banner_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, [
			                   'nullable' => false,
			                   'primary'  => true,
		                   ], 'Banner ID')
		                   ->addColumn('store_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, [
			                   'unsigned' => true,
			                   'nullable' => false,
			                   'primary'  => true,
		                   ], 'Store ID')
		                   ->addIndex($installer->getIdxName('althea_cms_banner_store', ['store_id']), ['store_id'])
		                   ->addForeignKey(
			                   $installer->getFkName('althea_cms_banner_store', 'banner_id', 'althea_cms_banner', 'banner_id'),
			                   'banner_id',
			                   $installer->getTable('althea_cms_banner'),
			                   'banner_id',
			                   \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
		                   )
		                   ->addForeignKey(
			                   $installer->getFkName('althea_cms_banner_store', 'store_id', 'store', 'store_id'),
			                   'store_id',
			                   $installer->getTable('store'),
			                   'store_id',
			                   \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
		                   )
		                   ->setComment('Althea CMS Banner To Store Linkage Table');
		$installer->getConnection()->createTable($table);

		$installer->endSetup();
	}

}