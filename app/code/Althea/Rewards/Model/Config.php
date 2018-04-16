<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 05/02/2018
 * Time: 4:01 PM
 */

namespace Althea\Rewards\Model;

use Magento\Store\Model\ScopeInterface;

class Config extends \Mirasvit\Rewards\Model\Config {

	const XML_PATH_IS_SHOW_REFERRAL_SHARE = 'rewards/display_options/is_show_referral_share';

	public function getDisplayOptionsIsShowReferralShare($store = null)
	{
		return $this->scopeConfig->getValue(self::XML_PATH_IS_SHOW_REFERRAL_SHARE, ScopeInterface::SCOPE_STORE, $store);
	}

}