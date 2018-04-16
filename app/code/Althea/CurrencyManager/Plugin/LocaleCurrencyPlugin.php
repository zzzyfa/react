<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 21/11/2017
 * Time: 3:44 PM
 */

namespace Althea\CurrencyManager\Plugin;

use Althea\CurrencyManager\Helper\Data;
use Magento\Framework\Locale\Currency;

class LocaleCurrencyPlugin {

	protected $_helper;

	/**
	 * LocaleCurrencyPlugin constructor.
	 *
	 * @param \Althea\CurrencyManager\Helper\Data $helper
	 */
	public function __construct(Data $helper)
	{
		$this->_helper = $helper;
	}

	public function aroundGetCurrency(Currency $subject, \Closure $proceed, $currency)
	{
		$result  = $proceed($currency);
		$options = $this->_helper->getOptions(array(), true, $currency);

		$result->setFormat($options);

		return $result;
	}

}