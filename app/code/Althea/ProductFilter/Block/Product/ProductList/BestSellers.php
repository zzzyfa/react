<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 08/03/2018
 * Time: 2:13 PM
 */

namespace Althea\ProductFilter\Block\Product\ProductList;

use Magento\Catalog\Block\Product\ListProduct;

class BestSellers extends ListProduct {

	const URL_KEY_BESTSELLERS = 'best-sellers';

	protected $_defaultToolbarBlock = 'Althea\ProductFilter\Block\Product\ProductList\Toolbar';
	protected $_collection;

	/**
	 * @inheritDoc
	 */
	protected function _getProductCollection()
	{
		if (!$this->_collection) {

			$collection = parent::_getProductCollection();

			$collection->getSelect()->joinLeft(
				['bestsellers' => 'sales_bestsellers_aggregated_yearly'],
				sprintf('bestsellers.product_id = e.entity_id AND bestsellers.store_id = %s', $this->_storeManager->getStore()->getId()),
				'qty_ordered'
			);

			$collection->getSelect()->columns('MAX(cat_index.category_id) AS max_cat_id');
			$collection->getSelect()->group('e.entity_id');
			$collection->getSelect()->order('max_cat_id DESC');
//			$collection->getSelect()->order('cat_index_position DESC');
			$collection->getSelect()->order('bestsellers.qty_ordered DESC');

			$this->_collection = $collection;
		}

		return $this->_collection;
	}

}