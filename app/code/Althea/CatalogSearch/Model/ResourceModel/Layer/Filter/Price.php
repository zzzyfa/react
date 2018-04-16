<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 23/03/2018
 * Time: 3:51 PM
 */

namespace Althea\CatalogSearch\Model\ResourceModel\Layer\Filter;

class Price extends \Magento\Catalog\Model\ResourceModel\Layer\Filter\Price {

	protected $_layer;
	protected $_session;
	protected $_storeManager;

	/**
	 * @inheritDoc
	 */
	public function __construct(
		\Magento\Framework\Model\ResourceModel\Db\Context $context,
		\Magento\Framework\Event\ManagerInterface $eventManager,
		\Magento\Catalog\Model\Layer\Resolver $layerResolver,
		\Magento\Customer\Model\Session $session,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		string $connectionName = null
	)
	{
		parent::__construct($context, $eventManager, $layerResolver, $session, $storeManager, $connectionName);

		$this->_layer        = $layerResolver->get();
		$this->_session      = $session;
		$this->_storeManager = $storeManager;
	}

	/**
	 * @inheritDoc
	 */
	public function getCount($range)
	{
		$select = $this->_getSelect();
		$priceExpression = $this->_getFullPriceExpression($select);

		/**
		 * Check and set correct variable values to prevent SQL-injections
		 */
		$range = floatval($range);
		if ($range == 0) {
			$range = 1;
		}
		$countExpr = new \Zend_Db_Expr('COUNT(*)');
		$rangeExpr = new \Zend_Db_Expr("FLOOR(({$priceExpression}) / {$range}) + 1");

		$select->columns(['range' => $rangeExpr, 'count' => $countExpr]);
		// althea:
		// - fix incorrect usage of ` while integrating multiselect filter
//		$select->group($rangeExpr)->order("({$rangeExpr}) ASC");
		$select->group($rangeExpr)->order(["range ASC"]);

		return $this->getConnection()->fetchPairs($select);
	}

	/**
	 * @inheritDoc
	 */
	protected function _getSelect()
	{
		$collection = $this->_layer->getProductCollection();
		$collection->addPriceData(
			$this->_session->getCustomerGroupId(),
			$this->_storeManager->getStore()->getWebsiteId()
		);

		if ($collection->getCatalogPreparedSelect() !== null) {
			$select = clone $collection->getCatalogPreparedSelect();
		} else {
			$select = clone $collection->getSelect();
		}

		// reset columns, order and limitation conditions
		$select->reset(\Magento\Framework\DB\Select::COLUMNS);
		$select->reset(\Magento\Framework\DB\Select::ORDER);
		$select->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
		$select->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);

		// remove join with main table
		$fromPart = $select->getPart(\Magento\Framework\DB\Select::FROM);
		if (!isset(
				$fromPart[\Magento\Catalog\Model\ResourceModel\Product\Collection::INDEX_TABLE_ALIAS]
			) || !isset(
				$fromPart[\Magento\Catalog\Model\ResourceModel\Product\Collection::MAIN_TABLE_ALIAS]
			)
		) {
			return $select;
		}

		// processing FROM part
		$priceIndexJoinPart = $fromPart[\Magento\Catalog\Model\ResourceModel\Product\Collection::INDEX_TABLE_ALIAS];
		$priceIndexJoinConditions = explode('AND', $priceIndexJoinPart['joinCondition']);
		$priceIndexJoinPart['joinType'] = \Magento\Framework\DB\Select::FROM;
		$priceIndexJoinPart['joinCondition'] = null;
		$fromPart[\Magento\Catalog\Model\ResourceModel\Product\Collection::MAIN_TABLE_ALIAS] = $priceIndexJoinPart;
		unset($fromPart[\Magento\Catalog\Model\ResourceModel\Product\Collection::INDEX_TABLE_ALIAS]);
		$select->setPart(\Magento\Framework\DB\Select::FROM, $fromPart);
		// althea:
		// - unset search_result join for facet
		if (array_key_exists('search_result', $fromPart)) {
			unset($fromPart['search_result']);
		}
		foreach ($fromPart as $key => $fromJoinItem) {
			$fromPart[$key]['joinCondition'] = $this->_replaceTableAlias($fromJoinItem['joinCondition']);
		}
		$select->setPart(\Magento\Framework\DB\Select::FROM, $fromPart);

		// processing WHERE part
		$wherePart = $select->getPart(\Magento\Framework\DB\Select::WHERE);
		foreach ($wherePart as $key => $wherePartItem) {

			// althea:
			// - unset price_index filter for facet
			if (strpos($wherePartItem, 'price_index') !== false) {

				unset($wherePart[$key]);
			} else {

				$wherePart[$key] = $this->_replaceTableAlias($wherePartItem);
			}
		}
		$select->setPart(\Magento\Framework\DB\Select::WHERE, $wherePart);
		$excludeJoinPart = \Magento\Catalog\Model\ResourceModel\Product\Collection::MAIN_TABLE_ALIAS . '.entity_id';
		foreach ($priceIndexJoinConditions as $condition) {
			if (strpos($condition, $excludeJoinPart) !== false) {
				continue;
			}
			$select->where($this->_replaceTableAlias($condition));
		}
		$select->where($this->_getPriceExpression($select) . ' IS NOT NULL');

		return $select;
	}

	/**
	 * @inheritDoc
	 */
	public function applyPriceRange(\Magento\Catalog\Model\Layer\Filter\FilterInterface $filter, $interval)
	{
		if (!$interval) {
			return $this;
		}

		foreach ($interval as $index => $item) {

			list($from, $to) = $item;
			if ($from === '' && $to === '') {
				return $this;
			}

			$select = $filter->getLayer()->getProductCollection()->getSelect();
			$priceExpr = $this->_getPriceExpression($select, false);

			if ($to !== '') {
				$to = (double)$to;
				if ($from == $to) {
					$to += self::MIN_POSSIBLE_PRICE;
				}
			}

			if ($from !== '') {
				if ($index < 1) {

					$select->where($priceExpr . ' >= ' . $this->_getComparingValue($from));
				} else {

					$select->orWhere($priceExpr . ' >= ' . $this->_getComparingValue($from));
				}
			}
			if ($to !== '') {
				$select->where($priceExpr . ' < ' . $this->_getComparingValue($to));
			}
		}

		return $this;
	}

}