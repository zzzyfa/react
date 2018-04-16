<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 14/02/2018
 * Time: 3:23 PM
 */

namespace Althea\Customer\Block\Address;

use Magento\Framework\Exception\NoSuchEntityException;

class Edit extends \Magento\Customer\Block\Address\Edit {

	/**
	 * @inheritDoc
	 */
	protected function _prepareLayout()
	{
		// Init address object
		if ($addressId = $this->getRequest()->getParam('id')) {
			try {
				$this->_address = $this->_addressRepository->getById($addressId);
				if ($this->_address->getCustomerId() != $this->_customerSession->getCustomerId()) {
					$this->_address = null;
				}
			} catch (NoSuchEntityException $e) {
				$this->_address = null;
			}
		}

		if ($this->_address === null || !$this->_address->getId()) {
			$this->_address = $this->addressDataFactory->create();
			$customer = $this->getCustomer();
			$this->_address->setPrefix($customer->getPrefix());
			$this->_address->setFirstname($customer->getFirstname());
			$this->_address->setMiddlename($customer->getMiddlename());
			$this->_address->setLastname($customer->getLastname());
			$this->_address->setSuffix($customer->getSuffix());
		}

		$this->pageConfig->getTitle()->set($this->getTitle());

		if ($postedData = $this->_customerSession->getAddressFormData(true)) {
			$postedData['region'] = [
				'region_id' => $postedData['region_id'],
				'region' => (!empty($postedData['region'])) ? $postedData['region'] : '', // althea: disabled input field is not submitted
			];
			$this->dataObjectHelper->populateWithArray(
				$this->_address,
				$postedData,
				'\Magento\Customer\Api\Data\AddressInterface'
			);
		}

		return $this;
	}

}