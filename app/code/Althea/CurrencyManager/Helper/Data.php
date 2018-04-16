<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 17/08/2017
 * Time: 5:50 PM
 */

namespace Althea\CurrencyManager\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\DataObject;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper {

	protected $_options         = array();
	protected $_optionsadvanced = array();
	protected $_storeManager;
	protected $_configHelper;

	public function __construct(Context $context, StoreManagerInterface $storeManager, Config $configHelper)
	{
		$this->_storeManager = $storeManager;
		$this->_configHelper = $configHelper;

		parent::__construct($context);
	}

	public function getOptions($options = array(), $old = false, $currency = "default") //$old for support Magento 1.3.x
	{
		$storeId = $this->_storeManager->getStore()->getId();

		if ((!isset($this->_options[$storeId][$currency]))
			|| (!isset($this->_optionsadvanced[$storeId][$currency]))
		) {

			$this->setOptions($currency);
		}

		$newOptions         = $this->_options[$storeId][$currency];
		$newOptionsAdvanced = $this->_optionsadvanced[$storeId][$currency];

		if (!$old) {

			$newOptions = $newOptions + $newOptionsAdvanced;
		}

		// For JavaScript prices: Strange Symbol extracting in function getOutputFormat
		// in file app/code/core/Mage/Directory/Model/Currency.php
		// For Configurable, Bundle and Simple with custom options
		// This causes problem if any currency has by default NO_SYMBOL
		// with this module can't change display value in this case
		if (isset($options["display"])) {

			if ($options["display"] == \Zend_Currency::NO_SYMBOL) {

				unset($newOptions["display"]);
			}
		}

		if (count($options) > 0) {

			return $newOptions + $options;
		} else {

			return $newOptions;
		}
	}

	public function clearOptions($options)
	{
		$oldOptions = array("position", "format", "display", "precision", "script", "name", "currency", "symbol");

		foreach (array_keys($options) as $optionKey) {

			if (!in_array($optionKey, $oldOptions)) {

				unset($options[$optionKey]);
			}
		}

		return $options;
	}

	public function isEnabled()
	{
		$storeId = $this->_storeManager->getStore()->getId();

		if ($storeId > 0) {

			return $this->_configHelper->getGeneralEnabled($storeId);
		}

		return $this->_configHelper->getGeneralEnabledAdmin();
	}

	public function setOptions($currency = "default")
	{
		$options         = array();
		$optionsAdvanced = array();
		$storeId         = $this->_storeManager->getStore()->getId();

		if ($this->isEnabled()) {

			$notCheckout = !($this->_configHelper->getGeneralExcludeCheckout() & $this->isInOrder());

			$this->_getGeneralOptions($options, $optionsAdvanced, $notCheckout);

			// formatting symbols from admin, preparing to use. Maybe can do better :)
			// если в админке будут внесены
			// несколько значений для одной валюты,
			// то использоваться будет только одна
			$symbolReplace = $this->_configHelper->getGeneralSymbolReplace();

			if (null !== $symbolReplace) {

				$this->_collectCurrencyOptions($currency, $notCheckout, $options, $optionsAdvanced);
			}
		} // end NOT ENABLED

		$this->_options[$storeId][$currency]         = $options;
		$this->_optionsadvanced[$storeId][$currency] = $optionsAdvanced;

		if (!isset($this->_options[$storeId]["default"])) {

			$this->_options[$storeId]["default"]         = $options;
			$this->_optionsadvanced[$storeId]["default"] = $optionsAdvanced;
		}

		return $this;
	}

	protected function _getGeneralOptions(&$options, &$optionsAdvanced, $notCheckout)
	{
		$precision   = $this->_configHelper->getGeneralPrecision();
		$zeroText    = $this->_configHelper->getGeneralZeroText();
		$position    = $this->_configHelper->getGeneralPosition();
		$display     = $this->_configHelper->getGeneralDisplay();
		$inputAdmin  = $this->_configHelper->getGeneralInputAdmin();
		$suffix      = $this->_configHelper->getGeneralCutZeroDecimalSuffix();
		$minDecCount = $this->_configHelper->getGeneralMinDecimalCount();

		if ($notCheckout) {

			if (null !== $precision) { // precision must be in range -1 .. 30

				$options['precision'] = min(30, max(-1, (int)$precision));
			}

			if (null !== $zeroText) {

				$optionsAdvanced['zerotext'] = $zeroText;
			}
		}

		if (null !== $position) {

			$options['position'] = (int)$position;
		}

		if (null !== $display) {

			$options['display'] = (int)$display;
		}

		if (null !== $inputAdmin) {

			if ($inputAdmin > 0) {

				$optionsAdvanced['input_admin'] = (int)$inputAdmin;
			}
		}

		$optionsAdvanced['excludecheckout']       = $this->_configHelper->getGeneralExcludeCheckout();
		$optionsAdvanced['cutzerodecimal']        = $this->_configHelper->getGeneralCutZeroDecimal();
		$optionsAdvanced['cutzerodecimal_suffix'] = (null !== $suffix) ? $suffix : "";
		$optionsAdvanced['min_decimal_count']     = (null !== $minDecCount) ? $minDecCount : "2";
	}

	protected function _collectCurrencyOptions($currency, $notCheckout, &$options, &$optionsAdvanced)
	{
		$symbolReplace = $this->_unsetSymbolReplace();

		if (count($symbolReplace['currency']) > 0) {

			$tmpOptions                           = array();
			$tmpOptionsAdvanced                   = array();
			$tmpOptionsAdvanced['cutzerodecimal'] = $this->_getCurrencyOption($currency, $symbolReplace, 'cutzerodecimal', true);

			if (isset($symbolReplace['cutzerodecimal_suffix'])) {

				$tmpOptionsAdvanced["cutzerodecimal_suffix"] = $this->_getCurrencyOption($currency, $symbolReplace, 'cutzerodecimal_suffix');
			}

			if (isset($symbolReplace['min_decimal_count'])) {

				$tmpOptionsAdvanced["min_decimal_count"] = $this->_getCurrencyOption($currency, $symbolReplace, 'min_decimal_count');
			}

			$tmpOptions['position'] = $this->_getCurrencyOption($currency, $symbolReplace, 'position', true);
			$tmpOptions['display']  = $this->_getCurrencyOption($currency, $symbolReplace, 'display', true);
			$tmpOptions['symbol']   = $this->_getCurrencyOption($currency, $symbolReplace, 'symbol');

			if ($notCheckout) {

				$tmpOptionsAdvanced['zerotext'] = $this->_getCurrencyOption($currency, $symbolReplace, 'zerotext');
				$precision                      = $this->_getCurrencyOption($currency, $symbolReplace, 'precision', true);

				if ($precision !== false) {

					$tmpOptions['precision'] = min(30, max(-1, $precision));
				}
			}

			foreach ($tmpOptions as $option => $value) {

				if ($value !== false) {

					$options[$option] = $value;
				}
			}

			foreach ($tmpOptionsAdvanced as $option => $value) {

				if ($value !== false) {

					$optionsAdvanced[$option] = $value;
				}
			}
		}
	}

	/**
	 * To check where price is used
	 * in some cases default values for precision and zerotext should be used
	 * for sales/checkout in frontend
	 * for admin AND sales_order*
	 *
	 * @return bool
	 */
	public function isInOrder()
	{
		$moduleName           = $this->_getRequest()->getModuleName();
		$controllerName       = $this->_getRequest()->getActionName();
		$orderModules         = array('sales', 'checkout', 'paypal');
		$modifiedOrderModules = array(
			'order_modules' => new DataObject(array('module_names' => $orderModules)),
		);

		$this->_eventManager->dispatch('et_currencymanager_checking_is_in_order_before', $modifiedOrderModules);

		$orderModules = $modifiedOrderModules['order_modules']->getData('module_names');

		return ((in_array($moduleName, $orderModules))
			|| (
				($moduleName == 'admin') && (strpos($controllerName, 'sales_order') !== false)
			));
	}

	protected function _unsetSymbolReplace()
	{
		$symbolReplace = $this->_configHelper->getGeneralSymbolReplace();

		if (!is_array($symbolReplace)) {

			$symbolReplace = unserialize($symbolReplace);

			foreach (array_keys($symbolReplace['currency']) as $symbolReplaceKey) {

				if (strlen(trim($symbolReplace['currency'][$symbolReplaceKey])) == 0) {

					unset($symbolReplace['currency'][$symbolReplaceKey]);
					unset($symbolReplace['precision'][$symbolReplaceKey]);
					unset($symbolReplace['min_decimal_count'][$symbolReplaceKey]);
					unset($symbolReplace['cutzerodecimal'][$symbolReplaceKey]);
					unset($symbolReplace['cutzerodecimal_suffix'][$symbolReplaceKey]);
					unset($symbolReplace['position'][$symbolReplaceKey]);
					unset($symbolReplace['display'][$symbolReplaceKey]);
					unset($symbolReplace['symbol'][$symbolReplaceKey]);
					unset($symbolReplace['zerotext'][$symbolReplaceKey]);
				}
			}

			return $symbolReplace;
		}

		return false;
	}

	public function resetOptions()
	{
		$this->_options         = array();
		$this->_optionsadvanced = array();
	}

	protected function _getCurrencyOption($currency, $symbolReplace, $option, $int = false)
	{
		$configSubData = array_combine($symbolReplace['currency'], $symbolReplace[$option]);

		if (array_key_exists($currency, $configSubData)) {

			$value = $configSubData[$currency];

			if ($value === "") {

				return false;
			}

			return ($int) ? (int)$value : $value;
		}

		return false;
	}

}