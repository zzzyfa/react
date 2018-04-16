<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 09/01/2018
 * Time: 5:59 PM
 */

namespace Althea\Customer\Model\ResourceModel;

use Magento\Framework\Exception\InputException;

class AddressRepository extends \Magento\Customer\Model\ResourceModel\AddressRepository {

	/**
	 * @inheritDoc
	 */
	public function save(\Magento\Customer\Api\Data\AddressInterface $address)
	{
		$addressModel = null;
		$customerModel = $this->customerRegistry->retrieve($address->getCustomerId());
		if ($address->getId()) {
			$addressModel = $this->addressRegistry->retrieve($address->getId());
		}

		if ($addressModel === null) {
			/** @var \Magento\Customer\Model\Address $addressModel */
			$addressModel = $this->addressFactory->create();
			$addressModel->updateData($address);
			$addressModel->setCustomer($customerModel);
		} else {
			$addressModel->updateData($address);
		}

		$inputException = $this->_customValidate($addressModel); // althea: override validation
		if ($inputException->wasErrorAdded()) {
			throw $inputException;
		}
		$addressModel->save();
		$address->setId($addressModel->getId());
		// Clean up the customer registry since the Address save has a
		// side effect on customer : \Magento\Customer\Model\ResourceModel\Address::_afterSave
		$this->customerRegistry->remove($address->getCustomerId());
		$this->addressRegistry->push($addressModel);
		$customerModel->getAddressesCollection()->clear();

		return $addressModel->getDataModel();
	}

	protected function _customValidate(\Magento\Customer\Model\Address $customerAddressModel)
	{
		$exception = new InputException();

		if ($customerAddressModel->getShouldIgnoreValidation()) {

			return $exception;
		}

		if (!\Zend_Validate::is($customerAddressModel->getFirstname(), 'NotEmpty')) {

			$exception->addError(__('%fieldName is a required field.', ['fieldName' => 'firstname']));
		}

		if (!\Zend_Validate::is($customerAddressModel->getLastname(), 'NotEmpty')) {

			$exception->addError(__('%fieldName is a required field.', ['fieldName' => 'lastname']));
		}

		if (!\Zend_Validate::is($customerAddressModel->getStreetLine(1), 'NotEmpty')) {

			$exception->addError(__('%fieldName is a required field.', ['fieldName' => 'street']));
		}

		if (!\Zend_Validate::is($customerAddressModel->getCity(), 'NotEmpty')) {

			$exception->addError(__('%fieldName is a required field.', ['fieldName' => 'city']));
		}

		if (!\Zend_Validate::is($customerAddressModel->getTelephone(), 'NotEmpty')) {

			$exception->addError(__('%fieldName is a required field.', ['fieldName' => 'telephone']));
		}

		$havingOptionalZip = $this->directoryData->getCountriesWithOptionalZip();

		if (!in_array($customerAddressModel->getCountryId(), $havingOptionalZip)
			&& !\Zend_Validate::is($customerAddressModel->getPostcode(), 'NotEmpty')
		) {

			$exception->addError(__('%fieldName is a required field.', ['fieldName' => 'postcode']));
		}

		if (!\Zend_Validate::is($customerAddressModel->getCountryId(), 'NotEmpty')) {

			$exception->addError(__('%fieldName is a required field.', ['fieldName' => 'countryId']));
		}

		if ($this->directoryData->isRegionRequired($customerAddressModel->getCountryId())) {

			$regionCollection = $customerAddressModel->getCountryModel()->getRegionCollection();

			// althea: all customer addresses will be saved while saving using CustomerRepository class, old addresses do not have region_id
			if (empty($customerAddressModel->getRegion())) {

				if (!$regionCollection->count()) {

					$exception->addError(__('%fieldName is a required field.', ['fieldName' => 'region']));
				} else if (!in_array(
					$customerAddressModel->getRegionId(),
					array_column($regionCollection->getData(), 'region_id')
				)) {

					$exception->addError(__('%fieldName is a required field.', ['fieldName' => 'regionId']));
				}
			}
		}

		return $exception;
	}

}