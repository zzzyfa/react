<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 14/02/2018
 * Time: 11:53 AM
 */

namespace Althea\Paypal\Plugin\Model;

class ConfigPlugin {

	/**
	 * Currency codes supported by PayPal methods
	 *
	 * @var string[]
	 */
	protected $_extraSupportedCurrencyCodes = [
		'PHP',
	];

	public function aroundIsCurrencyCodeSupported(\Magento\Paypal\Model\Config $subject, \Closure $proceed, $code)
	{
		$result = $proceed($code);

		if (in_array($code, $this->_extraSupportedCurrencyCodes)) {

			$result = true;
		}

		return $result;
	}

}