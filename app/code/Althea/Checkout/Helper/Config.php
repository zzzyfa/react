<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 08/02/2018
 * Time: 12:22 PM
 */

namespace Althea\Checkout\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper {

	const XML_PATH_MOBILE_FRONTEND_SHOW_ERROR     = 'checkout/mobile/frontend_display_error';
	const XML_PATH_MOBILE_ANDROID_INTENT_DEEPLINK = 'checkout/mobile/android_intent_deeplink';
	const XML_PATH_OPTIONS_ENABLE_LOGGING         = 'checkout/options/enable_logging';

	public function getIsFrontendShowError($storeId = null)
	{
		return $this->scopeConfig->getValue(self::XML_PATH_MOBILE_FRONTEND_SHOW_ERROR, ScopeInterface::SCOPE_STORES, $storeId);
	}

	public function getIsUseAndroidIntentDeepLink($storeId = null)
	{
		return $this->scopeConfig->getValue(self::XML_PATH_MOBILE_ANDROID_INTENT_DEEPLINK, ScopeInterface::SCOPE_STORES, $storeId);
	}

	public function getIsLogEnabled($storeId = null)
	{
		return $this->scopeConfig->getValue(self::XML_PATH_OPTIONS_ENABLE_LOGGING, ScopeInterface::SCOPE_STORES, $storeId);
	}

}