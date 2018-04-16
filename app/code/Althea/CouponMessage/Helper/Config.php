<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 14/07/2017
 * Time: 3:20 PM
 */

namespace Althea\CouponMessage\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;

class Config extends AbstractHelper {

	const XML_PATH_GENERAL_ENABLE           = 'couponmessage/general/is_enabled';
	const XML_PATH_MSG_DEFAULT              = 'couponmessage/general/msg_default';
	const XML_PATH_MSG_NOT_EXIST            = 'couponmessage/general/msg_not_exist';
	const XML_PATH_MSG_INVALID_RULE         = 'couponmessage/general/msg_invalid_rule';
	const XML_PATH_MSG_EXPIRED              = 'couponmessage/general/msg_expired';
	const XML_PATH_MSG_INVALID_CUST_GROUP   = 'couponmessage/general/msg_invalid_customer_group';
	const XML_PATH_MSG_OVERLIMIT            = 'couponmessage/general/msg_overlimit';
	const XML_PATH_MSG_OVERLIMIT_CUST_GROUP = 'couponmessage/general/msg_overlimit_customer_group';

	/**
	 * Get module status
	 *
	 * @param null $storeId
	 * @return mixed
	 */
	public function getGenerableEnable($storeId = null)
	{
		return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_ENABLE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $storeId);
	}

	/**
	 * Get default message
	 *
	 * @param $couponCode
	 * @return \Magento\Framework\Phrase
	 */
	public function getMsgDefault($couponCode)
	{
		return sprintf(__($this->scopeConfig->getValue(self::XML_PATH_MSG_DEFAULT)), $couponCode);
	}

	/**
	 * Get 'coupon code does not exist' message
	 *
	 * @param $couponCode
	 * @return \Magento\Framework\Phrase
	 */
	public function getMsgNotExist($couponCode)
	{
		if ($msg = $this->scopeConfig->getValue(self::XML_PATH_MSG_NOT_EXIST)) {

			return sprintf(__($msg), $couponCode);
		}

		return $this->getMsgDefault($couponCode);
	}

	/**
	 * Get 'unmet rule conditions' message
	 *
	 * @param $couponCode
	 * @return \Magento\Framework\Phrase
	 */
	public function getMsgInvalidRule($couponCode)
	{
		if ($msg = $this->scopeConfig->getValue(self::XML_PATH_MSG_INVALID_RULE)) {

			return sprintf(__($msg), $couponCode);
		}

		return $this->getMsgDefault($couponCode);
	}

	/**
	 * Get 'coupon code expired' message
	 *
	 * @param $couponCode
	 * @return \Magento\Framework\Phrase
	 */
	public function getMsgExpired($couponCode)
	{
		if ($msg = $this->scopeConfig->getValue(self::XML_PATH_MSG_EXPIRED)) {

			return sprintf(__($msg), $couponCode);
		}

		return $this->getMsgDefault($couponCode);
	}

	/**
	 * Get 'invalid customer group' message
	 *
	 * @param $couponCode
	 * @return \Magento\Framework\Phrase
	 */
	public function getMsgInvalidCustGroup($couponCode)
	{
		if ($msg = $this->scopeConfig->getValue(self::XML_PATH_MSG_INVALID_CUST_GROUP)) {

			return sprintf(__($msg), $couponCode);
		}

		return $this->getMsgDefault($couponCode);
	}

	/**
	 * Get 'usage limit exceeded' message
	 *
	 * @param $couponCode
	 * @return \Magento\Framework\Phrase
	 */
	public function getMsgOverlimit($couponCode)
	{
		if ($msg = $this->scopeConfig->getValue(self::XML_PATH_MSG_OVERLIMIT)) {

			return sprintf(__($msg), $couponCode);
		}

		return $this->getMsgDefault($couponCode);
	}

	/**
	 * Get 'usage limit exceeded (customer group)' message
	 *
	 * @param $couponCode
	 * @return \Magento\Framework\Phrase
	 */
	public function getMsgOverlimitCustGroup($couponCode)
	{
		if ($msg = $this->scopeConfig->getValue(self::XML_PATH_MSG_OVERLIMIT_CUST_GROUP)) {

			return sprintf(__($msg), $couponCode);
		}

		return $this->getMsgDefault($couponCode);
	}

}