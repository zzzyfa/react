<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 14/12/2017
 * Time: 12:12 PM
 */

namespace Althea\Catalog\Model\Category\Attribute\Source;

class Sortby extends \Magento\Catalog\Model\Category\Attribute\Source\Sortby {

	const SORT_TYPE_POPULAR       = 'bestselling';
	const SORT_TYPE_NEWEST        = 'newest';
	const SORT_TYPE_NAME_ASC      = 'name_asc';
	const SORT_TYPE_NAME_DESC     = 'name_desc';
	const SORT_TYPE_PRICE_HIGHEST = 'price_desc';
	const SORT_TYPE_PRICE_LOWEST  = 'price_asc';

	/**
	 * @inheritDoc
	 */
	public function getAllOptions()
	{
		if ($this->_options === null) {

			$this->_options = [
				[
					'label' => __('Best Sellers'),
					'value' => self::SORT_TYPE_POPULAR,
				],
				[
					'label' => __('New Arrivals'),
					'value' => self::SORT_TYPE_NEWEST,
				],
				[
					'label' => __('Alphabetically A-Z'),
					'value' => self::SORT_TYPE_NAME_ASC,
				],
				[
					'label' => __('Alphabetically Z-A'),
					'value' => self::SORT_TYPE_NAME_DESC,
				],
				[
					'label' => __('Price High to Low'),
					'value' => self::SORT_TYPE_PRICE_HIGHEST,
				],
				[
					'label' => __('Price Low to High'),
					'value' => self::SORT_TYPE_PRICE_LOWEST,
				],
			];

			foreach ($this->_getCatalogConfig()->getAttributesUsedForSortBy() as $attribute) {

				$this->_options[] = [
					'label' => __($attribute['frontend_label']),
					'value' => $attribute['attribute_code'],
				];
			}
		}

		return $this->_options;
	}

}