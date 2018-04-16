<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 11/08/2017
 * Time: 4:02 PM
 */

namespace Althea\Cms\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface AlertSearchResultsInterface extends SearchResultsInterface {

	/**
	 * Get alert list.
	 *
	 * @return \Althea\Cms\Api\Data\AlertInterface[]
	 */
	public function getItems();

	/**
	 * Set alerts list.
	 *
	 * @param \Althea\Cms\Api\Data\AlertInterface[] $items
	 * @return $this
	 */
	public function setItems(array $items);

}