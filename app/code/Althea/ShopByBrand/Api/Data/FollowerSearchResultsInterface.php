<?php
/**
 * Created by PhpStorm.
 * User: manadirmahi
 * Date: 17/10/2017
 * Time: 11:40 AM
 */

namespace Althea\ShopByBrand\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;


/**
 * Interface FollowerSearchResultsInterface
 * @package Althea\ShopByBrand\Api\Data
 */
interface FollowerSearchResultsInterface extends SearchResultsInterface
{

    /**
     * Get blocks list.
     *
     * @return \Althea\ShopByBrand\Api\Data\FollowerInterface[]
     */
    public function getItems();

    /**
     * Set blocks list.
     *
     * @param \Althea\ShopByBrand\Api\Data\FollowerInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}