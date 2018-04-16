<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 24/07/2017
 * Time: 4:40 PM
 */

namespace Althea\Nexmo\Model;

use Althea\Framework\Model\AbstractModelWithTimestamp;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer;

class Verification extends AbstractModelWithTimestamp {

	const STATUS_PENDING  = 0;
	const STATUS_VERIFIED = 1;

	protected $_eventPrefix = 'althea_nexmo_verification';

	protected function _construct()
	{
		$this->_init('Althea\Nexmo\Model\ResourceModel\Verification');
	}

	/**
	 * @param Customer|int $customer
	 * @return $this
	 */
	public function loadByCustomer($customer)
	{
		if ($customer instanceof Customer || $customer instanceof CustomerInterface) {

			$customerId = $customer->getId();
		} else {

			$customerId = (int)$customer;
		}

		$this->_getResource()->loadByCustomerId($this, $customerId);
		$this->_afterLoad();

		return $this;
	}

	/**
	 * @param Customer|int $customer
	 * @param string       $phoneNo
	 * @return $this
	 */
	public function loadByCustomerAndPhoneNo($customer, $phoneNo)
	{
		if ($customer instanceof Customer || $customer instanceof CustomerInterface) {

			$customerId = $customer->getId();
		} else {

			$customerId = (int)$customer;
		}

		$this->_getResource()->loadByCustomerIdAndPhoneNo($this, $customerId, $phoneNo);
		$this->_afterLoad();

		return $this;
	}

	/**
	 * @param Customer|int $customer
	 * @return $this
	 */
	public function loadActiveByCustomer($customer)
	{
		if ($customer instanceof Customer || $customer instanceof CustomerInterface) {

			$customerId = $customer->getId();
		} else {

			$customerId = (int)$customer;
		}

		$this->_getResource()->loadActiveByCustomerId($this, $customerId);
		$this->_afterLoad();

		return $this;
	}

	/**
	 * @param string $phoneNo
	 * @param int    $websiteId
	 * @return $this
	 */
	public function loadActiveByPhoneNo($phoneNo, $websiteId)
	{
		$this->_getResource()->loadActiveByPhoneNo($this, $phoneNo, $websiteId);
		$this->_afterLoad();

		return $this;
	}

	/**
	 * @param string $requestId
	 * @return $this
	 */
	public function loadUnverifiedByRequestId($requestId)
	{
		$this->_getResource()->loadUnverifiedByRequestId($this, $requestId);
		$this->_afterLoad();

		return $this;
	}

}