<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 29/11/2017
 * Time: 6:00 PM
 */

namespace Althea\InventorySuccess\Model\ResourceModel;

use Magestore\InventorySuccess\Api\Db\QueryProcessorInterface;

class InstallManagement extends \Magestore\InventorySuccess\Model\ResourceModel\InstallManagement {

	/**
	 * @inheritDoc
	 */
	protected function _updateOrderItems()
	{
		$products = [];
		//$needToShipOrderIds = $this->getNeedToShipOrderIds();
		$connection = $this->getConnection();
		$warehouseId = $this->_getDefaultWarehouseId();
		/* Get order items */
		//$orderCondition = $connection->prepareSqlCondition('order_id', ['in' => $needToShipOrderIds]);
		$select = $connection->select()->from(['main_table' => $this->getTable('sales_order_item')], [
			'item_id',
			'order_id',
			'product_id',
			'qty_ordered',
			'qty_canceled',
			'subtotal' => 'base_row_total',
			'qty_to_ship' => "IF(qty_ordered-qty_shipped-qty_refunded-qty_canceled > '0', qty_ordered-qty_shipped-qty_refunded-qty_canceled, 0)",
		])
			//->where($orderCondition)
			//->where('qty_ordered-qty_shipped-qty_refunded-qty_canceled > ?', 0)
			                 ->where('product_type = ?', \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
		                     ->joinLeft(['order' => $this->getTable('sales_order')],
			                     'main_table.order_id = order.entity_id', [
				                     'created_at', 'updated_at'
			                     ])
		->where('order.state NOT IN (?)', ['complete', 'canceled', 'closed']) // althea: ignore complete, canceled, closed order state
		->where('order.status NOT IN (?)', ['shipped_cod']); // althea: ignore shipped_cod order status
		$query = $connection->query($select);
		$ids = array();
		while ($row = $query->fetch()) {
			$ids[] = $row['item_id'];
			/* prepare qty_to_ship data of product in warehouse */
			$productId = $row['product_id'];
			$qtyToShip = $row['qty_to_ship'];
			if (isset($products[$productId])) {
				$qtyToShip += $products[$productId]['qty_to_ship'];
			}
			$products[$productId] = ['product_id' => $productId, 'qty_to_ship' => $qtyToShip];
		}
		if (count($ids)) {
			/* prepare query to update in sales_order_item table */
			$updateValues = array(
				'warehouse_id' => $warehouseId,
			);
			$field = 'item_id';
			$where = $connection->quoteInto($this->getTable('sales_order_item') . '.' . $field . ' IN (?) ', $ids);
			$this->_queryProcessor->addQuery(array(
				'type' => QueryProcessorInterface::QUERY_TYPE_UPDATE,
				'values' => $updateValues,
				'condition' => $where,
				'table' => $this->getTable('sales_order_item')
			));
		}
		return $products;
	}

}