<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 21/11/2017
 * Time: 3:41 PM
 */

namespace Althea\CurrencyManager\Plugin;

use Althea\CurrencyManager\Helper\Data;
use Magento\Framework\Locale\Format;
use Magento\Framework\Locale\Resolver;

class LocaleFormatPlugin {

	protected $_helper;
	protected $_localeResolver;

	/**
	 * LocaleFormatPlugin constructor.
	 *
	 * @param \Althea\CurrencyManager\Helper\Data $helper
	 * @param \Magento\Framework\Locale\Resolver  $resolver
	 */
	public function __construct(Data $helper, Resolver $resolver)
	{
		$this->_helper         = $helper;
		$this->_localeResolver = $resolver;
	}

	public function aroundGetPriceFormat(Format $subject, \Closure $proceed, $localeCode = null, $currencyCode = null)
	{
		$result  = $proceed($localeCode, $currencyCode);
		$options = $this->_helper->getOptions(array());

		if (isset($options["precision"])) {

			$result["requiredPrecision"] = $options["precision"];
			$result["precision"]         = $options["precision"];
		}

		$localeCode = $localeCode ?: $this->_localeResolver->getLocale();

		if ('id_ID' === $localeCode) { // althea: magento2 uses ICU DATA in php compiler to get currency info

			$result['decimalSymbol'] = '.';
			$result['groupSymbol']   = ',';
		}

		return $result;
	}

}