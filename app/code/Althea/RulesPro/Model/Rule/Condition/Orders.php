<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 20/09/2017
 * Time: 10:53 AM
 */

namespace Althea\RulesPro\Model\Rule\Condition;

use Magento\Framework\App\ResourceConnection as AppResource;
use Magento\Rule\Model\Condition;

class Orders extends \Amasty\RulesPro\Model\Rule\Condition\Orders {

	const NUM_ORDER_PROCESSING             = 'order_num_processing';
	const NUM_ORDER_PENDING                = 'order_num_pending';
	const NUM_ORDER_PROCESSING_COD         = 'order_num_processing_cod';
	const NUM_ORDER_PREPARING_SHIPPING_COD = 'order_num_preparing_shipping_cod';
	const NUM_ORDER_SHIPPED_COD            = 'order_num_shipped_cod';

	private $resource;

	public function __construct(Condition\Context $context, AppResource $resource, array $data = [])
	{
		$this->resource = $resource;

		parent::__construct($context, $resource, $data);
	}

	public function loadAttributeOptions()
	{
		$attributes = array(
			'order_num'                            => __('Number of Completed Orders'),
			// althea: added custom conditions (order statuses)
			self::NUM_ORDER_PROCESSING             => __('Number of Processing Orders'),
			self::NUM_ORDER_PENDING                => __('Number of Pending Orders'),
			self::NUM_ORDER_PROCESSING_COD         => __('Number of Processing(CoD) Orders'),
			self::NUM_ORDER_PREPARING_SHIPPING_COD => __('Number of Preparing Shipping(CoD) Orders'),
			self::NUM_ORDER_SHIPPED_COD            => __('Number of Shipped(CoD) Orders'),
			'sales_amount'                         => __('Total Sales Amount'),
		);

		$this->setAttributeOption($attributes);

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function validate(\Magento\Framework\Model\AbstractModel $model)
	{
		$quote = $model;

		if (!$quote instanceof \Magento\Quote\Model\Quote) {

			$quote = $model->getQuote();
		}

		$num = 0;

		if ($quote->getCustomerId()) {

			$db     = $this->resource->getConnection('default');
			$select = $db->select()
			             ->from(array('o' => $this->resource->getTableName('sales_order')), array())
			             ->where('o.customer_id = ?', $quote->getCustomerId());

			if ('order_num' == $this->getAttribute()) {

				$select->from(null, array(new \Zend_Db_Expr('COUNT(*)')))
				       ->where('o.status = ?', 'complete');
			} else if (self::NUM_ORDER_PROCESSING == $this->getAttribute()) { // althea: added custom conditions (order statuses)

				$select->from(null, array(new \Zend_Db_Expr('COUNT(*)')))
				       ->where('o.status = ?', 'processing');
			} else if (self::NUM_ORDER_PENDING == $this->getAttribute()) {

				$select->from(null, array(new \Zend_Db_Expr('COUNT(*)')))
				       ->where('o.status = ?', 'pending');
			} else if (self::NUM_ORDER_PROCESSING_COD == $this->getAttribute()) {

				$select->from(null, array(new \Zend_Db_Expr('COUNT(*)')))
				       ->where('o.status = ?', 'processing_cod');
			} else if (self::NUM_ORDER_PREPARING_SHIPPING_COD == $this->getAttribute()) {

				$select->from(null, array(new \Zend_Db_Expr('COUNT(*)')))
				       ->where('o.status = ?', 'preparing_shipping_cod');
			} else if (self::NUM_ORDER_SHIPPED_COD == $this->getAttribute()) {

				$select->from(null, array(new \Zend_Db_Expr('COUNT(*)')))
				       ->where('o.status = ?', 'shipped_cod');
			} else if ('sales_amount' == $this->getAttribute()) {

				$select->from(null, array(new \Zend_Db_Expr('SUM(o.base_grand_total)')))
				       ->where('o.status = ?', 'complete');
			}

			$num = $db->fetchOne($select);
		}

		return $this->validateAttribute($num);
	}

}