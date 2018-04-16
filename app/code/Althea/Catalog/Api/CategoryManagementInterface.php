<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 14/12/2017
 * Time: 3:40 PM
 */

namespace Althea\Catalog\Api;

interface CategoryManagementInterface extends \Magento\Catalog\Api\CategoryManagementInterface {

	/**
	 * Retrieve list of categories
	 *
	 * @param int $rootCategoryId
	 * @param int $depth
	 *
	 * @throws \Magento\Framework\Exception\NoSuchEntityException If ID is not found
	 *
	 * @return \Althea\Catalog\Api\Data\CategoryTreeInterface containing Tree objects
	 */
	public function getTree($rootCategoryId = null, $depth = null);

}