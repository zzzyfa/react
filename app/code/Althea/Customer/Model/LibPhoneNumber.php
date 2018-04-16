<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 13/11/2017
 * Time: 6:27 PM
 */

namespace Althea\Customer\Model;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use Althea\Customer\Api\LibPhoneNumberInterface;

class LibPhoneNumber implements LibPhoneNumberInterface
{

	/**
	 * @inheritDoc
	 */
	public function validatePhone($phoneNumber, $countryCode)
	{
		if(is_null($phoneNumber) || is_null($countryCode) ) {
			return false;
		}

		$phoneNumber = trim($phoneNumber);
		$countryCode = trim($countryCode);

        if(preg_match("/[a-zA-Z]/",$phoneNumber)) {
            return false;
        }

		if(strlen($countryCode) > 2) {
			$countryCode = substr($countryCode, 0, 2);
		}

		$formatter = PhoneNumberUtil::getInstance();

		try {
			$instance = $formatter->parse($phoneNumber, $countryCode);

			// check if number is valid
			if (!$formatter->isValidNumber($instance)) {
				return false;
			}
		} catch (NumberParseException $e) {
			return false;
		}
		return true;
	}
}