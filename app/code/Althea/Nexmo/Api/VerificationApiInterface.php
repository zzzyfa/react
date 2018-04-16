<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 03/08/2017
 * Time: 12:15 PM
 */

namespace Althea\Nexmo\Api;

interface VerificationApiInterface {

	/**
	 * Request verification code
	 *
	 * @param string $phoneNo
	 * @param string $countryCode
	 * @param int    $customerId
	 *
	 * @return string
	 */
	public function request($phoneNo, $countryCode, $customerId);

	/**
	 * Verify verification code
	 *
	 * @param string $requestId
	 * @param string $code
	 * @param string $customerId
	 *
	 * @return string
	 */
	public function verify($requestId, $code, $customerId);

}