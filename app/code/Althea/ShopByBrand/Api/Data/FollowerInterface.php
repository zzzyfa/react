<?php
/**
 * Created by PhpStorm.
 * User: manadirmahi
 * Date: 12/10/2017
 * Time: 11:29 AM
 */

namespace Althea\ShopByBrand\Api\Data;

interface FollowerInterface {

	const FOLLOWER_ID = 'follower_id';
	const CUSTOMER_ID = 'customer_id';
	const BRAND_ID    = 'brand_id';
	const IS_ACTIVE   = 'is_active';
	const CREATED_AT  = 'created_at';
	const UPDATED_AT  = 'updated_at';

	/**
	 * @return int|null
	 */
	public function getFollowerId();

	/**
	 * @return int|null
	 */
	public function getCustomerId();

	/**
	 * @return int|null
	 */
	public function getBrandId();

	/**
	 * @return bool|null
	 */
	public function getIsActive();

	/**
	 * @return string|null
	 */
	public function getCreatedAt();

	/**
	 * @return string|null
	 */
	public function getUpdatedAt();

	/**
	 * @param int $follower_id
	 *
	 * @return FollowerInterface
	 */
	public function setFollowerId($follower_id);

	/**
	 * @param int $customer_id
	 *
	 * @return FollowerInterface
	 */
	public function setCustomerId($customer_id);

	/**
	 * @param string $brand_id
	 *
	 * @return FollowerInterface
	 */
	public function setBrandId($brand_id);

	/**
	 * @param bool|int $isActive
	 *
	 * @return FollowerInterface
	 */
	public function setIsActive($isActive);

	/**
	 * @param string $createdAt
	 *
	 * @return FollowerInterface
	 */
	public function setCreatedAt($createdAt);

	/**
	 * @param string $updatedAt
	 *
	 * @return FollowerInterface
	 */
	public function setUpdatedAt($updatedAt);

}