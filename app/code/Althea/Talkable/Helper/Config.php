<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 12/02/2018
 * Time: 10:02 AM
 */

namespace Althea\Talkable\Helper;

use Magento\Store\Model\ScopeInterface;

class Config extends \AltheaTech\Talkable\Helper\Config {

	const XML_PATH_GENERAL_API_KEY        = 'socialreferrals/general/api_key';
	const XML_PATH_GENERAL_ENABLE_LOGGING = 'socialreferrals/general/enable_logging';

	public function getApiKey($storeId = null)
	{
		return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_API_KEY, ScopeInterface::SCOPE_STORES, $storeId);
	}

	public function getIsLogEnabled($storeId = null)
	{
		return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_ENABLE_LOGGING, ScopeInterface::SCOPE_STORES, $storeId);
	}

}