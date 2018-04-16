<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 27/11/2017
 * Time: 4:38 PM
 */

namespace Althea\Customer\Api;

interface LibPhoneNumberInterface {

	/**
	 * Validate phone number.
	 *
	 * @param string $phoneNumber
	 * @param string $countryCode
	 * @return boolean
	 */
	public function validatePhone($phoneNumber, $countryCode);

}