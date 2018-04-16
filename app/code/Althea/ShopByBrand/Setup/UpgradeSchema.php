<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 13/11/2017
 * Time: 4:41 PM
 */

namespace Althea\ShopByBrand\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface {

	/**
	 * @inheritDoc
	 */
	public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
	{
		$setup->startSetup();

		if (version_compare($context->getVersion(), '1.0.1', '<')) {

			$this->changeWebsiteIdDataType($setup);
		}

		$setup->endSetup();
	}

	/**
	 * Change website_id (SMALLINT) to website_ids (TEXT)
	 *
	 * @param SchemaSetupInterface $setup
	 * @return void
	 */
	private function changeWebsiteIdDataType(SchemaSetupInterface $setup)
	{
		$connection = $setup->getConnection();

		$connection->changeColumn(
			$setup->getTable('tm_brand'),
			'website_id',
			'website_ids',
			[
				'type'     => Table::TYPE_TEXT,
				'nullable' => true,
				'comment'  => 'Websites',
			]
		);
	}

}