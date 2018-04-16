<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 22/03/2018
 * Time: 5:32 PM
 */

namespace Althea\CatalogSearch\Model\Layer\Search;

/**
 * Class FilterableAttributeList
 *
 * - based on Smile-SA/elasticsuite for reference
 *
 * @package Althea\CatalogSearch\Model\Layer\Search
 */
class FilterableAttributeList extends \Magento\Catalog\Model\Layer\Search\FilterableAttributeList {

	/**
	 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
	 * {@inheritDoc}
	 */
	protected function _prepareAttributeCollection($collection)
	{
		$collection->addSetInfo(true);
		$collection->addIsFilterableInSearchFilter()
		           ->addVisibleFilter();

		return $collection;
	}

}