<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 27/11/2017
 * Time: 4:38 PM
 */

namespace Althea\ShopByBrand\Api;

interface BrandFollowerRepositoryInterface {

	/**
	 * Retrieve brand.
	 *
	 * @return \Magento\Framework\Api\SearchResultsInterface
	 */
	public function getByCurrentStoreId();

	/**
	 * Get my brand list
	 *
	 * @param int $customerId
	 *
	 * @return \Magento\Framework\Api\SearchResultsInterface
	 */
	public function getMyList($customerId);

	/**
	 * Add follow with brand
	 *
	 * @param int $brandId
	 * @param int $customerId
	 *
	 * @return \Magento\Framework\Api\SearchResultsInterface
	 */
	public function addFollowByToken($brandId, $customerId);

	/**
	 * Remove follow with brand
	 *
	 * @param int $brandId
	 * @param int $customerId
	 *
	 * @return \Magento\Framework\Api\SearchResultsInterface
	 */
	public function removeFollowByToken($brandId, $customerId);


    /**
     * Get All product list in following
     *
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Catalog\Api\Data\ProductSearchResultsInterface
     */
    public function getFollowingProductList($customerId);

    /**
     * Get product list in following
     *
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Catalog\Api\Data\ProductSearchResultsInterface
     */
    public function getFollowingProductListByFilter($customerId, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}