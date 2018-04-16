<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 30/08/2017
 * Time: 2:53 PM
 */

namespace Althea\Catalog\Api\Data;

interface ProductInterface extends \Magento\Catalog\Api\Data\ProductInterface {

	/**
	 * product price by store
	 *
	 * @return string|null
	 */
	public function getStorePrice();

	/**
	 * special product price by store
	 *
	 * @return string|null
	 */
	public function getStoreSpecialPrice();

}