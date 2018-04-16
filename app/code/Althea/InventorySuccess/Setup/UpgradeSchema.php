<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 04/12/2017
 * Time: 10:33 AM
 */

namespace Althea\InventorySuccess\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface {

	/**
	 * @inheritDoc
	 */
	public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
	{
		if (version_compare($context->getVersion(), '1.0.1', '<')) {

			$this->_addCreatedAtIndexToSalesOrderItem($setup);
			$this->_addWarehouseIdIndexToSalesShipmentItem($setup);
		}
	}

	protected function _addCreatedAtIndexToSalesOrderItem(SchemaSetupInterface $setup)
	{
		$setup->getConnection()->addIndex(
			$setup->getTable('sales_order_item'),
			$setup->getIdxName('sales_order_item', ['created_at']),
			['created_at']
		);
	}

	protected function _addWarehouseIdIndexToSalesShipmentItem(SchemaSetupInterface $setup)
	{
		$setup->getConnection()->addIndex(
			$setup->getTable('sales_shipment_item'),
			$setup->getIdxName('sales_shipment_item', ['warehouse_id']),
			['warehouse_id']
		);
	}

}