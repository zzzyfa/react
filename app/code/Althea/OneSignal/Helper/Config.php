<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 18/07/2017
 * Time: 3:18 PM
 */

namespace Althea\OneSignal\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;

class Config extends AbstractHelper {

	const XML_PATH_GENERAL_ENABLE          = 'onesignal/general/is_enabled';
	const XML_PATH_ONESIGNAL_APP_ID        = 'onesignal/general/app_id';
	const XML_PATH_ONESIGNAL_SUBDOMAIN     = 'onesignal/general/subdomain';
	const XML_PATH_ONESIGNAL_SAFARI_WEB_ID = 'onesignal/general/safari_web_id';
	const XML_PATH_ONESIGNAL_AUTO_REGISTER = 'onesignal/general/auto_register';
	const XML_PATH_ONESIGNAL_SHOW_BUTTON   = 'onesignal/general/show_notify_button';

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
	 * Get 'auto prompt subscription for HTTPS' option
	 *
	 * @return mixed
	 */
	public function isAutoRegister()
	{
		return $this->scopeConfig->getValue(self::XML_PATH_ONESIGNAL_AUTO_REGISTER);
	}

	/**
	 * Get 'display notification button' option
	 *
	 * @return mixed
	 */
	public function isShowNotifyButton()
	{
		return $this->scopeConfig->getValue(self::XML_PATH_ONESIGNAL_SHOW_BUTTON);
	}

	/**
	 * Get application ID
	 *
	 * @return mixed
	 */
	public function getAppId()
	{
		return $this->scopeConfig->getValue(self::XML_PATH_ONESIGNAL_APP_ID);
	}

	/**
	 * Get safari web ID
	 *
	 * @return mixed
	 */
	public function getSafariWebId()
	{
		return $this->scopeConfig->getValue(self::XML_PATH_ONESIGNAL_SAFARI_WEB_ID);
	}

	/**
	 * Get OneSignal subdomain
	 *
	 * @return mixed
	 */
	public function getSubdomain()
	{
		return $this->scopeConfig->getValue(self::XML_PATH_ONESIGNAL_SUBDOMAIN);
	}

}