<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 06/03/2018
 * Time: 4:47 PM
 */

namespace Althea\ProductFilter\Block\Product\ProductList;

use Magento\Catalog\Block\Product\ListProduct;

class EverythingUnder extends ListProduct {

	protected $_defaultToolbarBlock = 'Althea\ProductFilter\Block\Product\ProductList\Toolbar';
	protected $_collection;

	/**
	 * @inheritDoc
	 */
	protected function _getProductCollection()
	{
		if (!$this->_collection) {

			$rootCatId = $this->getLayer()->getCurrentStore()->getRootCategoryId();

			$this->getLayer()->setCurrentCategory($rootCatId);

			$collection = parent::_getProductCollection();

			switch ($this->_storeManager->getWebsite()->getCode()) {

				case 'my':
					$underPrice = 15;
					break;
				case 'sg':
					$underPrice = 5;
					break;
				case 'ph':
					$underPrice = 150;
					break;
				case 'id':
					$underPrice = 50000;
					break;
				case 'th':
					$underPrice = 129;
					break;
				case 'us':
					$underPrice = 5;
					break;
				case 'tw':
					$underPrice = 100;
					break;
				default:
					$underPrice = 0;
			}

			$collection->addFieldToFilter('price', ['to' => $underPrice]);

			$this->_collection = $collection;
		}

		return $this->_collection;
	}

}