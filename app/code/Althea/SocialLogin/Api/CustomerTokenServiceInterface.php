<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 06/03/2018
 * Time: 2:12 PM
 */

namespace Althea\SocialLogin\Api;

interface CustomerTokenServiceInterface {

	/**
	 * @param string $code
	 * @param string $accessToken
	 * @return string
	 */
	public function createSocialCustomerAccessToken($code, $accessToken);

}