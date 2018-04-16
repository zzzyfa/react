<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 19/12/2017
 * Time: 10:53 AM
 */

namespace Althea\Catalog\Model\Config\Source;

use Althea\Catalog\Model\Category\Attribute\Source\Sortby;

class ListSort extends \Magento\Catalog\Model\Config\Source\ListSort {

	/**
	 * @inheritDoc
	 */
	public function toOptionArray()
	{
		$options   = [];
		$options[] = ['label' => __('Best Sellers'), 'value' => Sortby::SORT_TYPE_POPULAR];
		$options[] = ['label' => __('New Arrivals'), 'value' => Sortby::SORT_TYPE_NEWEST];
		$options[] = ['label' => __('Alphabetically A-Z'), 'value' => Sortby::SORT_TYPE_NAME_ASC];
		$options[] = ['label' => __('Alphabetically Z-A'), 'value' => Sortby::SORT_TYPE_NAME_DESC];
		$options[] = ['label' => __('Price High to Low'), 'value' => Sortby::SORT_TYPE_PRICE_HIGHEST];
		$options[] = ['label' => __('Price Low to High'), 'value' => Sortby::SORT_TYPE_PRICE_LOWEST];

		foreach ($this->_getCatalogConfig()->getAttributesUsedForSortBy() as $attribute) {

			$options[] = ['label' => __($attribute['frontend_label']), 'value' => $attribute['attribute_code']];
		}

		return $options;
	}

}