<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 22/03/2018
 * Time: 3:10 PM
 */

namespace Althea\CatalogSearch\Model\Layer\Filter;

/**
 * Interface FilterInterface
 *
 * - based on Smile-SA/elasticsuite for reference
 *
 * @package Althea\CatalogSearch\Model\Layer\Filter
 */
interface FilterInterface extends \Magento\Catalog\Model\Layer\Filter\FilterInterface {

	/**
	 * Add facet to the current layer products collection.
	 *
	 * @param array $config Facet config.
	 *
	 * @return $this
	 */
	public function addFacetToCollection($config = []);

}