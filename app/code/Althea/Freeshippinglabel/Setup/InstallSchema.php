<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 27/12/2017
 * Time: 3:19 PM
 */

namespace Althea\Freeshippinglabel\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface {

	/**
	 * @inheritDoc
	 */
	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
	{
		$this->addCustomColumns($setup);
	}

	private function addCustomColumns(SchemaSetupInterface $setup)
	{
		$setup->getConnection()->addColumn($setup->getTable('aw_fslabel_label'), 'min_item_qty', [
			'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			'nullable' => true,
			'comment'  => 'Mininum Cart Item Quantity',
			'default'  => 0,
			'after'    => 'goal',
		]);

		$setup->getConnection()->addColumn($setup->getTable('aw_fslabel_label'), 'identifier', [
			'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'length'   => 255,
			'nullable' => false,
			'comment'  => 'Label Identifier',
			'after'    => 'min_item_qty',
		]);

		$setup->getConnection()->addIndex(
			$setup->getTable('aw_fslabel_label'),
			$setup->getIdxName(
				$setup->getTable('aw_fslabel_label'),
				['identifier'],
				AdapterInterface::INDEX_TYPE_UNIQUE
			),
			['identifier'],
			AdapterInterface::INDEX_TYPE_UNIQUE
		);
	}

}