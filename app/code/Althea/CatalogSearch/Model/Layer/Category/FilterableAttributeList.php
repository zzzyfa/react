<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 22/03/2018
 * Time: 3:03 PM
 */

namespace Althea\CatalogSearch\Model\Layer\Category;

/**
 * Class FilterableAttributeList
 *
 * - based on Smile-SA/elasticsuite for reference
 *
 * @package Althea\CatalogSearch\Model\Layer\Category
 */
class FilterableAttributeList extends \Magento\Catalog\Model\Layer\Category\FilterableAttributeList {

	/**
	 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
	 * {@inheritDoc}
	 */
	protected function _prepareAttributeCollection($collection)
	{
		$collection->addSetInfo(true);
		$collection->addIsFilterableFilter();

		return $collection;
	}

}