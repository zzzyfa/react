<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 30/08/2017
 * Time: 3:59 PM
 */

namespace Althea\Catalog\Api\Data;

interface ProductSearchResultsInterface extends \Magento\Catalog\Api\Data\ProductSearchResultsInterface {

	/**
	 * Get attributes list.
	 *
	 * @return \Althea\Catalog\Api\Data\ProductInterface[]
	 */
	public function getItems();

	/**
	 * Set attributes list.
	 *
	 * @param \Magento\Catalog\Api\Data\ProductInterface[] $items
	 * @return $this
	 */
	public function setItems(array $items);

}