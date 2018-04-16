<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 16/08/2017
 * Time: 5:36 PM
 */

namespace Althea\CurrencyManager\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;

class Config extends AbstractHelper {

	const XML_PATH_GENERAL_ENABLED               = 'currencymanager/general/enabled';
	const XML_PATH_GENERAL_ENABLEDADM            = 'currencymanager/general/enabledadm';
	const XML_PATH_GENERAL_INPUT_ADMIN           = 'currencymanager/general/input_admin';
	const XML_PATH_GENERAL_EXCLUDECHECKOUT       = 'currencymanager/general/excludecheckout';
	const XML_PATH_GENERAL_CURRENCY              = 'currencymanager/general/currency';
	const XML_PATH_GENERAL_ZEROTEXT              = 'currencymanager/general/zerotext';
	const XML_PATH_GENERAL_SYMBOL                = 'currencymanager/general/symbol';
	const XML_PATH_GENERAL_SYMBOL_REPLACE        = 'currencymanager/general/symbolreplace';
	const XML_PATH_GENERAL_DISPLAY               = 'currencymanager/general/display';
	const XML_PATH_GENERAL_POSITION              = 'currencymanager/general/position';
	const XML_PATH_GENERAL_CUTZERODECIMAL_SUFFIX = 'currencymanager/general/cutzerodecimal_suffix';
	const XML_PATH_GENERAL_MIN_DECIMAL_COUNT     = 'currencymanager/general/min_decimal_count';
	const XML_PATH_GENERAL_CUTZERODECIMAL        = 'currencymanager/general/cutzerodecimal';
	const XML_PATH_GENERAL_PRECISION             = 'currencymanager/general/precision';

	/**
	 * @param null $storeId
	 * @return mixed
	 */
	public function getGeneralEnabled($storeId = null)
	{
		return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_ENABLED, ScopeInterface::SCOPE_STORE, $storeId);
	}

	/**
	 * @return mixed
	 */
	public function getGeneralEnabledAdmin()
	{
		return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_ENABLEDADM, ScopeInterface::SCOPE_STORE, Store::DEFAULT_STORE_ID);
	}

	/**
	 * @return mixed
	 */
	public function getGeneralInputAdmin()
	{
		return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_INPUT_ADMIN, ScopeInterface::SCOPE_STORE, Store::DEFAULT_STORE_ID);
	}

	/**
	 * @param null $storeId
	 * @return mixed
	 */
	public function getGeneralExcludeCheckout($storeId = null)
	{
		return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_EXCLUDECHECKOUT, ScopeInterface::SCOPE_STORE, $storeId);
	}

	/**
	 * @param null $storeId
	 * @return mixed
	 */
	public function getGeneralCurrency($storeId = null)
	{
		return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_CURRENCY, ScopeInterface::SCOPE_STORE, $storeId);
	}

	/**
	 * @param null $storeId
	 * @return mixed
	 */
	public function getGeneralSymbolReplace($storeId = null)
	{
		return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_SYMBOL_REPLACE, ScopeInterface::SCOPE_STORE, $storeId);
	}

	/**
	 * @param null $storeId
	 * @return mixed
	 */
	public function getGeneralZeroText($row = null, $storeId = null)
	{
		$path = self::XML_PATH_GENERAL_ZEROTEXT;

		if (null !== $row) {

			$path = sprintf("%s/%s", $path, $row);
		}

		return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeId);
	}

	/**
	 * @param null $storeId
	 * @return mixed
	 */
	public function getGeneralSymbol($row = null, $storeId = null)
	{
		$path = self::XML_PATH_GENERAL_SYMBOL;

		if (null !== $row) {

			$path = sprintf("%s/%s", $path, $row);
		}

		return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeId);
	}

	/**
	 * @param null $storeId
	 * @return mixed
	 */
	public function getGeneralDisplay($row = null, $storeId = null)
	{
		$path = self::XML_PATH_GENERAL_DISPLAY;

		if (null !== $row) {

			$path = sprintf("%s/%s", $path, $row);
		}

		return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeId);
	}

	/**
	 * @param null $storeId
	 * @return mixed
	 */
	public function getGeneralPosition($row = null, $storeId = null)
	{
		$path = self::XML_PATH_GENERAL_POSITION;

		if (null !== $row) {

			$path = sprintf("%s/%s", $path, $row);
		}

		return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeId);
	}

	/**
	 * @param null $storeId
	 * @return mixed
	 */
	public function getGeneralCutZeroDecimalSuffix($row = null, $storeId = null)
	{
		$path = self::XML_PATH_GENERAL_CUTZERODECIMAL_SUFFIX;

		if (null !== $row) {

			$path = sprintf("%s/%s", $path, $row);
		}

		return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeId);
	}

	/**
	 * @param null $storeId
	 * @return mixed
	 */
	public function getGeneralMinDecimalCount($row = null, $storeId = null)
	{
		$path = self::XML_PATH_GENERAL_MIN_DECIMAL_COUNT;

		if (null !== $row) {

			$path = sprintf("%s/%s", $path, $row);
		}

		return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeId);
	}

	/**
	 * @param null $storeId
	 * @return mixed
	 */
	public function getGeneralCutZeroDecimal($row = null, $storeId = null)
	{
		$path = self::XML_PATH_GENERAL_CUTZERODECIMAL;

		if (null !== $row) {

			$path = sprintf("%s/%s", $path, $row);
		}

		return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeId);
	}

	/**
	 * @param null $storeId
	 * @return mixed
	 */
	public function getGeneralPrecision($row = null, $storeId = null)
	{
		$path = self::XML_PATH_GENERAL_PRECISION;

		if (null !== $row) {

			$path = sprintf("%s/%s", $path, $row);
		}

		return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeId);
	}

}