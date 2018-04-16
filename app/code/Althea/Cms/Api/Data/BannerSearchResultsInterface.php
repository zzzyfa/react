<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 07/08/2017
 * Time: 3:29 PM
 */

namespace Althea\Cms\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface BannerSearchResultsInterface extends SearchResultsInterface {

	/**
	 * Get banner list.
	 *
	 * @return \Althea\Cms\Api\Data\BannerInterface[]
	 */
	public function getItems();

	/**
	 * Set banners list.
	 *
	 * @param \Althea\Cms\Api\Data\BannerInterface[] $items
	 * @return $this
	 */
	public function setItems(array $items);

}