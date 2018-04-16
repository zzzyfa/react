<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 24/07/2017
 * Time: 11:25 AM
 */

namespace Althea\Nexmo\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;

class Config extends AbstractHelper {

	const XML_PATH_GENERAL_ENABLE                       = 'nexmo/general/is_enabled';
	const XML_PATH_GENERAL_API_KEY                      = 'nexmo/general/api_key';
	const XML_PATH_GENERAL_API_SECRET                   = 'nexmo/general/api_secret';
	const XML_PATH_GENERAL_PIN_EXPIRY                   = 'nexmo/general/pin_expiry';
	const XML_PATH_GENERAL_EXP_MSG_REQUEST_NOT_CANCELED = 'nexmo/general/exception_msg_request_not_canceled';
	const XML_PATH_WEB_IS_CHECKOUT_VERIFICATION_ENABLED = 'nexmo/web/is_checkout_verification_enabled';

	/**
	 * Get module status
	 *
	 * @param null $storeId
	 * @return mixed
	 */
	public function getGeneralEnable($storeId = null)
	{
		return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_ENABLE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $storeId);
	}

	/**
	 * Get API key
	 *
	 * @param null $storeId
	 * @return mixed
	 */
	public function getApiKey($storeId = null)
	{
		return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_API_KEY, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $storeId);
	}

	/**
	 * Get API secret
	 *
	 * @param null $storeId
	 * @return mixed
	 */
	public function getApiSecret($storeId = null)
	{
		return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_API_SECRET, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $storeId);
	}

	/**
	 * Get custom 'unable to cancel request' exception message
	 *
	 * @param null $storeId
	 * @return mixed
	 */
	public function getRequestNotCanceledExceptionMsg($storeId = null)
	{
		return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_EXP_MSG_REQUEST_NOT_CANCELED, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $storeId);
	}

	/**
	 * Get pin expiry duration
	 *
	 * @param null $storeId
	 * @return mixed
	 */
	public function getPinExpiry($storeId = null)
	{
		return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_PIN_EXPIRY, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $storeId);
	}

	/**
	 * Get web checkout nexmo verification status
	 *
	 * @param null $storeId
	 * @return mixed
	 */
	public function getIsCheckoutVerificationEnabled($storeId = null)
	{
		return $this->scopeConfig->getValue(self::XML_PATH_WEB_IS_CHECKOUT_VERIFICATION_ENABLED, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $storeId);
	}

}