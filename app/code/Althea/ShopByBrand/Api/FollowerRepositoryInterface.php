<?php
/**
 * Created by PhpStorm.
 * User: manadirmahi
 * Date: 09/10/2017
 * Time: 6:20 PM
 */

namespace Althea\ShopByBrand\Api;

/**
 * Interface FollowerRepositoryInterface
 *
 * @package Althea\ShopByBrand\Api
 */
interface FollowerRepositoryInterface {

	/**
	 * @param int $customerId
	 * @return \TemplateMonster\ShopByBrand\Api\Data\BrandInterface[]
	 */
	public function getFollowingBrands($customerId);

}
