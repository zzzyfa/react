<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 14/12/2017
 * Time: 12:40 PM
 */

namespace Althea\Catalog\Model;

use Althea\Catalog\Model\Category\Attribute\Source\Sortby;

class Config extends \Magento\Catalog\Model\Config {

	protected $_options;

	/**
	 * @inheritDoc
	 */
	public function getAttributeUsedForSortByArray()
	{
		$options = $this->getCustomAttributeUsedForSortByArray();

		foreach ($this->getAttributesUsedForSortBy() as $attribute) {

			/* @var $attribute \Magento\Eav\Model\Entity\Attribute\AbstractAttribute */
			$options[$attribute->getAttributeCode()] = $attribute->getStoreLabel();
		}

		return $options;
	}

	public function getCustomAttributeUsedForSortByArray()
	{
		if (!$this->_options) {

			$this->_options = [
				Sortby::SORT_TYPE_POPULAR       => __('Best Sellers'),
				Sortby::SORT_TYPE_NEWEST        => __('New Arrivals'),
				Sortby::SORT_TYPE_NAME_ASC      => __('Alphabetically A-Z'),
				Sortby::SORT_TYPE_NAME_DESC     => __('Alphabetically Z-A'),
				Sortby::SORT_TYPE_PRICE_HIGHEST => __('Price High to Low'),
				Sortby::SORT_TYPE_PRICE_LOWEST  => __('Price Low to High'),
			];
		}

		return $this->_options;
	}

}