<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 30/11/2017
 * Time: 6:07 PM
 */

namespace Althea\InventorySuccess\Model\Service\Sales;

class PendingOrderItemService extends \Magestore\InventorySuccess\Model\Service\Sales\PendingOrderItemService {

	/**
	 * @inheritDoc
	 */
	public function getCollection($productId = null)
	{
		/* Start SQL : select all simple products and group by product_id , if exist parent_item_id -> calculate in configuration products */
		$collection = $this->orderItemFactory->create()
		                                     ->getCollection()
		                                     ->addFieldToFilter('product_id', $productId);

		$collection->getSelect()
		           ->columns(array(
			           'pending_qty' => new \Zend_Db_Expr($this->_pendingQty),
		           ))
		           ->where("{$this->_pendingQty} > 0")
		           ->where('product_type = ?', \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
		$collection->getSelect()
		           ->joinLeft(
			           array('order' => $this->cr->getTableName('sales_order')),
			           'main_table.order_id = order.entity_id',
			           array('increment_id' => 'order.increment_id')
		           )
		           ->where('order.state NOT IN (?)', ['complete', 'canceled', 'closed'])// althea: ignore complete, canceled, closed order state
		           ->where('order.status NOT IN (?)', ['shipped_cod'])// althea: ignore shipped_cod order status
		           ->order('main_table.item_id DESC');

		return $collection;
	}

}