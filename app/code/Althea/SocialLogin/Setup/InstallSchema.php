<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 06/03/2018
 * Time: 11:15 AM
 */

namespace Althea\SocialLogin\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface {

	/**
	 * @inheritDoc
	 */
	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
	{
		$installer = $setup;

		$installer->startSetup();
		$installer->getConnection()->addColumn(
			$setup->getTable('social_login_provider'),
			'website_id',
			[
				'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
				'unsigned' => true,
				'nullable' => false,
				'default'  => 0,
				'after'    => 'provider_id',
				'comment'  => 'Website ID',
			]
		);
		$installer->getConnection()->addForeignKey(
			$setup->getFkName(
				'social_login_provider',
				'website_id',
				'store_website',
				'website_id'
			),
			$setup->getTable('social_login_provider'),
			'website_id',
			$setup->getTable('store_website'),
			'website_id',
			\Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
		);
		$installer->endSetup();
	}

}