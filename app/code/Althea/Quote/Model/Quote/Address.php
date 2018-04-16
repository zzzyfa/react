<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 04/04/2018
 * Time: 9:40 AM
 */

namespace Althea\Quote\Model\Quote;

class Address extends \Magento\Quote\Model\Quote\Address {

	/**
	 * @inheritDoc
	 */
	public function validate()
	{
		$errors = [];
		if (!\Zend_Validate::is($this->getFirstname(), 'NotEmpty')) {
			$errors[] = __('Please enter the first name.');
		}

		if (!\Zend_Validate::is($this->getLastname(), 'NotEmpty')) {
			$errors[] = __('Please enter the last name.');
		}

		if (!\Zend_Validate::is($this->getStreetLine(1), 'NotEmpty')) {
			$errors[] = __('Please enter the street.');
		}

		if (!\Zend_Validate::is($this->getCity(), 'NotEmpty')) {
			$errors[] = __('Please enter the city.');
		}

		if (!\Zend_Validate::is($this->getTelephone(), 'NotEmpty')) {
			$errors[] = __('Please enter the phone number.');
		}

		$_havingOptionalZip = $this->_directoryData->getCountriesWithOptionalZip();
		if (!in_array(
				$this->getCountryId(),
				$_havingOptionalZip
			) && !\Zend_Validate::is(
				$this->getPostcode(),
				'NotEmpty'
			)
		) {
			$errors[] = __('Please enter the zip/postal code.');
		}

		if (!\Zend_Validate::is($this->getCountryId(), 'NotEmpty')) {
			$errors[] = __('Please enter the country.');
		}

		if ($this->getCountryModel()->getRegionCollection()->getSize()
			&& !\Zend_Validate::is($this->getRegionId(), 'NotEmpty')
			&& !\Zend_Validate::is($this->getRegion(), 'NotEmpty') // althea: needed only region / region_id
			&& $this->_directoryData->isRegionRequired($this->getCountryId())
		) {
			$errors[] = __('Please enter the state/province.');
		}

		if (empty($errors) || $this->getShouldIgnoreValidation()) {
			return true;
		}
		return $errors;
	}


}