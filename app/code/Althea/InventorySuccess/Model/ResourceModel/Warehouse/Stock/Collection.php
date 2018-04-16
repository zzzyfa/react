<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 30/11/2017
 * Time: 5:08 PM
 * -------------------------
 * Added feature : Jungho Park
 * Description   : Calculate 3 methods (processing_qty, pending_qty, canceled_qty)
 * Date          : 17/01/2018
 */

namespace Althea\InventorySuccess\Model\ResourceModel\Warehouse\Stock;

use Magento\Framework\DB\Select;
use Magestore\InventorySuccess\Api\Data\Warehouse\ProductInterface as WarehouseProductInterface;
use Magestore\InventorySuccess\Model\ResourceModel\Warehouse\Product as WarehouseProductResource;

class Collection extends \Magestore\InventorySuccess\Model\ResourceModel\Warehouse\Stock\Collection
{
    const STATE_PENDING = "pending";

    const MAPPING_FIELD = [
        'processing_qty' => 'SUM(IF(sales_order.status = \''.\Magento\Sales\Model\Order::STATE_PROCESSING.'\', qty_ordered, 0))',
        'pending_qty' => 'SUM(IF(sales_order.status = \''.self::STATE_PENDING.'\', qty_ordered, 0))',
        'canceled_qty' => 'SUM(IF(sales_order.status = \''.\Magento\Sales\Model\Order::STATE_CANCELED.'\', qty_ordered, 0))'
    ];

    protected function _initSelect()
    {
         parent::_initSelect();

        $statusCollection = $this->getConnection()->select()
            ->from(
                ['sales_order_item' => $this->getTable('sales_order_item')],
                ['product_id',
                    'pending_qty' => self::MAPPING_FIELD['pending_qty'],
                    'processing_qty' => self::MAPPING_FIELD['processing_qty'],
                    'canceled_qty' => self::MAPPING_FIELD['canceled_qty']])
            ->where('sales_order.status in (?)', [
                \Magento\Sales\Model\Order::STATE_PROCESSING,
                \Magento\Sales\Model\Order::STATE_CANCELED,
                self::STATE_PENDING
            ])
            ->joinLeft(['sales_order' => $this->getTable('sales_order')], 'sales_order_item.order_id = sales_order.entity_id', 'status')
            ->group('product_id');

        $this->getSelect()->join(['status_collection' => $statusCollection], 'status_collection.product_id = warehouse_product.product_id', '');

        $this->getSelect()->columns([
            'pending_qty' => new \Zend_Db_Expr('pending_qty'),
            'processing_qty' => new \Zend_Db_Expr('processing_qty'),
            'canceled_qty' => new \Zend_Db_Expr('canceled_qty'),
        ]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addQtyToFilter($columnName, $filterValue)
    {
        if (array_key_exists($columnName, self::MAPPING_FIELD)) {

            if (isset($filterValue['from'])) {

                $this->getSelect()->having($columnName . ' >= ?', $filterValue['from']);
            }

            if (isset($filterValue['to'])) {

                $this->getSelect()->having($columnName . ' <= ?', $filterValue['to']);
            }

            return $this;
        }

        return parent::addQtyToFilter($columnName, $filterValue);
    }

    /**
     * @inheritDoc
     */
    public function getBestSellerProducts($numberProduct, $warehouseId = null)
    {
        if ($warehouseId) {
            $this->getSelect()->where('warehouse_product.' . WarehouseProductInterface::WAREHOUSE_ID . ' = ?', $warehouseId);
        }

        // althea: use inner join instead of left join to speed up query
        // althea: avoid using SELECT * for unused columns
        $this->getSelect()->join(
            ['warehouse_shipment_item' => $this->getTable('sales_shipment_item')],
            'e.entity_id = warehouse_shipment_item.product_id AND warehouse_product.' . WarehouseProductInterface::WAREHOUSE_ID . ' = warehouse_shipment_item.warehouse_id',
            ''
        );
        $this->getSelect()->reset(Select::COLUMNS); // althea: remove unused SELECT columns
        $this->getSelect()->columns([
            'sku' => 'e.sku', // althea: add required SELECT column
            'available_qty' => new \Zend_Db_Expr('warehouse_product.qty'), // althea: add required SELECT column
            'total_qty_shipped' => new \Zend_Db_Expr(parent::MAPPING_FIELD['total_qty_shipped']),
        ]);
        $this->getSelect()->order(new \Zend_Db_Expr(parent::MAPPING_FIELD['total_qty_shipped'] . ' DESC'));

        return $this->setPageSize($numberProduct)->setCurPage(1);
    }

    /**
     * @inheritDoc
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        if (array_key_exists($field, self::MAPPING_FIELD)) {

            return parent::sortByTotalQty($field, $direction);
        }

        return parent::setOrder($field, $direction);
    }
}