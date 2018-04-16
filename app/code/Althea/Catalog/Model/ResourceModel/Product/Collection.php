<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 26/03/2018
 * Time: 2:08 PM
 */

namespace Althea\Catalog\Model\ResourceModel\Product;

use Althea\Catalog\Model\Category\Attribute\Source\Sortby;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Framework\Data\Collection\AbstractDb;

class Collection extends ProductCollection {

	/**
	 * @inheritDoc
	 */
	public function addOrder($field, $direction = self::SORT_ORDER_DESC)
	{
		switch ($field) {

			case Sortby::SORT_TYPE_POPULAR:
				return $this->getSelect()
				     ->joinLeft(
					     ['bestseller' => 'sales_bestsellers_aggregated_yearly'],
					     sprintf('e.entity_id = bestseller.product_id AND bestseller.store_id IN (%s)', $this->getStoreId()),
					     []
				     )
				     ->group('e.entity_id')
				     ->order(sprintf("bestseller.qty_ordered %s", $direction));
				break;
			case Sortby::SORT_TYPE_NAME_ASC:
				return parent::addOrder('name', ProductCollection::SORT_ORDER_ASC);
				break;
			case Sortby::SORT_TYPE_NAME_DESC:
				return parent::addOrder('name', $direction);
				break;
			case Sortby::SORT_TYPE_PRICE_LOWEST:
				return parent::addOrder('price', ProductCollection::SORT_ORDER_ASC);
				break;
			case Sortby::SORT_TYPE_PRICE_HIGHEST:
				return parent::addOrder('price', $direction);
				break;
			case Sortby::SORT_TYPE_NEWEST:
				return parent::addOrder('created_at', $direction);
				break;
		}

		return parent::addOrder($field, $direction);
	}

}