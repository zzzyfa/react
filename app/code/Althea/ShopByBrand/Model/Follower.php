<?php
/**
 * Created by PhpStorm.
 * User: manadirmahi
 * Date: 12/10/2017
 * Time: 11:29 AM
 */

namespace Althea\ShopByBrand\Model;

use Althea\ShopByBrand\Api\Data\FollowerInterface;
use Magento\Framework\Model\AbstractModel;

class Follower extends AbstractModel implements FollowerInterface {

	const CACHE_TAG = 'althea_shopbybrand_follower';

	/**
	 * @var string
	 */
	protected $_cacheTag = 'althea_shopbybrand_follower';

	/**
	 * @var string
	 */
	protected $_eventPrefix = 'althea_shopbybrand_follower';

	/**
	 * Initialize follower model
	 *
	 * @return void
	 */
	public function _construct()
	{
		$this->_init('Althea\ShopByBrand\Model\ResourceModel\Follower');
	}

	/**
	 * @return int|null
	 */
	public function getFollowerId()
	{
		return $this->getData(self::FOLLOWER_ID);
	}

	/**
	 * @return int|null
	 */
	public function getCustomerId()
	{
		return $this->getData(self::CUSTOMER_ID);
	}

	/**
	 * @return int|null
	 */
	public function getBrandId()
	{
		return $this->getData(self::BRAND_ID);
	}

	/**
	 * @return bool|null
	 */
	public function getIsActive()
	{
		return $this->getData(self::IS_ACTIVE);
	}

	/**
	 * @return string|null
	 */
	public function getCreatedAt()
	{
		return $this->getData(self::CREATED_AT);
	}

	/**
	 * @return string|null
	 */
	public function getUpdatedAt()
	{
		return $this->getData(self::UPDATED_AT);
	}

	/**
	 * @param int $follower_id
	 *
	 * @return FollowerInterface
	 */
	public function setFollowerId($follower_id)
	{
		return $this->setData(self::FOLLOWER_ID, $follower_id);
	}

	/**
	 * @param int $customer_id
	 *
	 * @return FollowerInterface
	 */
	public function setCustomerId($customer_id)
	{
		return $this->setData(self::CUSTOMER_ID, $customer_id);
	}

	/**
	 * @param string $brand_id
	 *
	 * @return FollowerInterface
	 */
	public function setBrandId($brand_id)
	{
		return $this->setData(self::BRAND_ID, $brand_id);
	}

	/**
	 * @param bool|int $isActive
	 *
	 * @return FollowerInterface
	 */
	public function setIsActive($isActive)
	{
		return $this->setData(self::IS_ACTIVE, $isActive);
	}

	/**
	 * @param string $createdAt
	 *
	 * @return FollowerInterface
	 */
	public function setCreatedAt($createdAt)
	{
		return $this->setData(self::CREATED_AT, $createdAt);
	}

	/**
	 * @param string $updatedAt
	 *
	 * @return FollowerInterface
	 */
	public function setUpdatedAt($updatedAt)
	{
		return $this->setData(self::UPDATED_AT, $updatedAt);
	}

}