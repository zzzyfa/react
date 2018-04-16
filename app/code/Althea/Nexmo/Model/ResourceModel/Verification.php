<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 24/07/2017
 * Time: 4:41 PM
 */

namespace Althea\Nexmo\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;

class Verification extends AbstractDb {

	protected function _construct()
	{
		$this->_init('althea_nexmo_verification', 'verification_id');
	}

	/**
	 * Load verification data by customer identifier
	 *
	 * @param \Althea\Nexmo\Model\Verification $verification
	 * @param int                              $customerId
	 * @return $this
	 */
	public function loadByCustomerId($verification, $customerId)
	{
		$connection = $this->getConnection();
		$select     = $this->_getLoadSelect('customer_id', $customerId, $verification)
		                   ->limit(1);
		$data       = $connection->fetchRow($select);

		if ($data) {

			$verification->setData($data);
		}

		$this->_afterLoad($verification);

		return $this;
	}

	/**
	 * Load verification data by customer identifier and phone number
	 *
	 * @param \Althea\Nexmo\Model\Verification $verification
	 * @param int                              $customerId
	 * @param string                           $phoneNo
	 * @return $this
	 */
	public function loadByCustomerIdAndPhoneNo($verification, $customerId, $phoneNo)
	{
		$connection = $this->getConnection();
		$select     = $this->_getLoadSelect('customer_id', $customerId, $verification)
		                   ->where('phone_number = ?', $phoneNo)
		                   ->limit(1);
		$data       = $connection->fetchRow($select);

		if ($data) {

			$verification->setData($data);
		}

		$this->_afterLoad($verification);

		return $this;
	}

	/**
	 * Load only verified verification by customer identifier
	 *
	 * @param \Althea\Nexmo\Model\Verification $verification
	 * @param int                              $customerId
	 * @return $this
	 */
	public function loadActiveByCustomerId($verification, $customerId)
	{
		$connection = $this->getConnection();
		$select     = $this->_getLoadSelect('customer_id', $customerId, $verification)
		                   ->where('status = ?', \Althea\Nexmo\Model\Verification::STATUS_VERIFIED)
		                   ->limit(1);
		$data       = $connection->fetchRow($select);

		if ($data) {

			$verification->setData($data);
		}

		$this->_afterLoad($verification);

		return $this;
	}

	/**
	 * Load only verified verification by website specific phone number
	 *
	 * @param \Althea\Nexmo\Model\Verification $verification
	 * @param string                           $phoneNo
	 * @param int                              $websiteId
	 * @return $this
	 */
	public function loadActiveByPhoneNo($verification, $phoneNo, $websiteId)
	{
		$connection = $this->getConnection();
		$select     = $this->_getLoadSelect('phone_number', $phoneNo, $verification)
		                   ->where('website_id = ?', $websiteId)
		                   ->where('status = ?', \Althea\Nexmo\Model\Verification::STATUS_VERIFIED);
		$data       = $connection->fetchRow($select);

		if ($data) {

			$verification->setData($data);
		}

		$this->_afterLoad($verification);

		return $this;
	}

	/**
	 * Load only unverified verification by request ID
	 *
	 * @param \Althea\Nexmo\Model\Verification $verification
	 * @param string                           $requestId
	 * @return $this
	 */
	public function loadUnverifiedByRequestId($verification, $requestId)
	{
		$connection = $this->getConnection();
		$select     = $this->_getLoadSelect('request_id', $requestId, $verification)
		                   ->where('status = ?', \Althea\Nexmo\Model\Verification::STATUS_PENDING);
		$data       = $connection->fetchRow($select);

		if ($data) {

			$verification->setData($data);
		}

		$this->_afterLoad($verification);

		return $this;
	}

}