<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 16/04/2018
 * Time: 10:52 AM
 */

namespace Althea\AutoCancelOrder\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper {

	const XML_PATH_GENERAL_ENABLE           = 'althea_autocancelorder/general/enable';
	const XML_PATH_OPTIONS_ENABLE_LOG       = 'althea_autocancelorder/general/log';
	const XML_PATH_GENERAL_ORDER_STATUSES   = 'althea_autocancelorder/general/order_statuses';
	const XML_PATH_GENERAL_PAYMENT_STATUSES = 'althea_autocancelorder/general/payment_statuses';

	/**
	 * Get module status
	 *
	 * @param null $storeId
	 * @return mixed
	 */
	public function getGeneralEnable($storeId = null)
	{
		return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_ENABLE, ScopeInterface::SCOPE_STORES, $storeId);
	}

	/**
	 * Get module enable log
	 *
	 * @param null $storeId
	 * @return mixed
	 */
	public function getIsLogEnabled($storeId = null)
	{
		return $this->scopeConfig->getValue(self::XML_PATH_OPTIONS_ENABLE_LOG, ScopeInterface::SCOPE_STORES, $storeId);
	}

	/**
	 * Get order statuses
	 *
	 * @param null $storeId
	 * @return mixed
	 */
	public function getOrderStatuses($storeId = null)
	{
		return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_ORDER_STATUSES, ScopeInterface::SCOPE_STORES, $storeId);
	}

	/**
	 * Get payment statuses
	 *
	 * @param null $storeId
	 * @return mixed
	 */
	public function getPaymentStatuses($storeId = null)
	{
		return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_PAYMENT_STATUSES, ScopeInterface::SCOPE_STORES, $storeId);
	}

}