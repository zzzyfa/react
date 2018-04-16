<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 30/08/2017
 * Time: 4:14 PM
 */

namespace Althea\Catalog\Api;

interface ProductRepositoryInterface extends \Magento\Catalog\Api\ProductRepositoryInterface {

	/**
	 * Get product list
	 *
	 * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
	 * @return \Althea\Catalog\Api\Data\ProductSearchResultsInterface
	 */
	public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

}