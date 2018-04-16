<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 07/09/2017
 * Time: 11:58 AM
 */

namespace Althea\PaymentFilter\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper {

	const XML_PATH_GENERAL_ENABLED               = 'paymentfilter/general/enabled';
	const XML_PATH_GENERAL_SHOW_DISABLED_METHODS = 'paymentfilter/general/show_disabled_methods';
	const XML_PATH_PAYMENT_METHODS               = 'payment';

	/**
	 * @param null $storeId
	 * @return mixed
	 */
	public function getGeneralEnabled($storeId = null)
	{
		return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_ENABLED, ScopeInterface::SCOPE_STORE, $storeId);
	}

	/**
	 * @param null $storeId
	 * @return mixed
	 */
	public function getGeneralShowDisabledMethods($storeId = null)
	{
		return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_SHOW_DISABLED_METHODS, ScopeInterface::SCOPE_STORE, $storeId);
	}

	/**
	 * @param null $storeId
	 * @return mixed
	 */
	public function getPaymentMethods($storeId = null)
	{
		return $this->scopeConfig->getValue(self::XML_PATH_PAYMENT_METHODS, ScopeInterface::SCOPE_STORE, $storeId);
	}

}