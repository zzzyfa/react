<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 22/08/2017
 * Time: 3:03 PM
 */

namespace Althea\CurrencyManager\Model;

use Althea\CurrencyManager\Helper\Config;
use Althea\CurrencyManager\Helper\Data;
use Magento\Directory\Model\Currency\FilterFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Locale\ResolverInterface;

class Currency extends \Magento\Directory\Model\Currency {

	protected $_helper;
	protected $_configHelper;
	protected $_request;
	protected $_locale;

	public function __construct(
		Data $helper,
		Config $configHelper,
		RequestInterface $request,
		ResolverInterface $locale,
		\Magento\Framework\Model\Context $context,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\Locale\FormatInterface $localeFormat,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Directory\Helper\Data $directoryHelper,
		FilterFactory $currencyFilterFactory,
		\Magento\Framework\Locale\CurrencyInterface $localeCurrency,
		\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
		\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
		array $data = []
	)
	{
		$this->_helper       = $helper;
		$this->_configHelper = $configHelper;
		$this->_request      = $request;
		$this->_locale       = $locale;

		parent::__construct($context, $registry, $localeFormat, $storeManager, $directoryHelper, $currencyFilterFactory, $localeCurrency, $resource, $resourceCollection, $data);
	}

	public function format($price, $options = array(), $includeContainer = true, $addBrackets = false)
	{
		if (method_exists($this, "formatPrecision")) {

			$options = $this->_helper->getOptions($options);

			return $this->formatPrecision(
				$price,
				isset($options["precision"]) ? $options["precision"] : 2,
				$this->_helper->clearOptions($options),
				$includeContainer,
				$addBrackets
			);
		}

		return parent::format($price, $options, $includeContainer, $addBrackets);
	}

	public function getOutputFormat()
	{
		$formatted = $this->formatTxt(10);
		$number    = $this->formatTxt(10, array('display' => \Zend_Currency::NO_SYMBOL));

		return str_replace($number, '%s', $formatted);
	}

	public function formatTxt($price, $options = array())
	{
		//$options['format'] = '#,##0.00¤'; // TODO: add ability to change format
		$answer = parent::formatTxt($price, $this->_helper->clearOptions($options));

		if ($this->_helper->isEnabled()) {

			$moduleName      = $this->_request->getModuleName();
			$optionsAdvanced = $this->_helper->getOptions($options, false, $this->getCurrencyCode());
			$options         = $this->_helper->getOptions($options, true, $this->getCurrencyCode());

			if (isset($options["precision"])) {

				$price = round($price, $options["precision"]);
			}

			$answer = parent::formatTxt($price, $options);

			if (count($options) > 0) {

				if (($moduleName == 'admin')) {

					$answer = parent::formatTxt($price, $this->_helper->clearOptions($options));
				}

				$minDecimalCount   = $optionsAdvanced['min_decimal_count'];
				$finalDecimalCount = $this->getPrecisionToCutZeroDecimals($price, $minDecimalCount);

				if (isset($options['precision'])) {

					if ($finalDecimalCount <= $options['precision']) {

						$options['precision'] = $finalDecimalCount;
					}
				}

				//check against -0
				$answer = $this->_formatWithPrecision($options, $optionsAdvanced, $price, $answer);

				if (!($this->_helper->isInOrder() && $optionsAdvanced['excludecheckout'])) {

					if ($price == 0) {

						if (isset($optionsAdvanced['zerotext']) && $optionsAdvanced['zerotext'] != "") {

							return $optionsAdvanced['zerotext'];
						}
					}

					$answer = $this->_cutZeroDecimal($options, $optionsAdvanced, $price, $answer);
				}
			}
		}

		return $answer;
	}

	protected function _formatWithPrecision($options, $optionsAdvanced, &$price, $answer)
	{
		if (isset($optionsAdvanced['precision'])) {

			$price = round($price, $optionsAdvanced['precision']);

			if ($optionsAdvanced['precision'] < 0) {

				$options['precision'] = 0;
			}

			//for correction -0 float zero
			if ($price == 0) {

				$price = 0;
			}

			//if no need to cut zero we must recreate default answer
			return parent::formatTxt($price, $this->_helper->clearOptions($options));
		}

		return $answer;
	}

	protected function _cutZeroDecimal($options, $optionsAdvanced, $price, $answer)
	{
		$zeroDecimal = (round($price, $optionsAdvanced['precision']) == round($price, 0));
		$suffix      = isset($optionsAdvanced['cutzerodecimal_suffix']) ? $optionsAdvanced['cutzerodecimal_suffix'] : "";

		if ($optionsAdvanced['cutzerodecimal'] && $zeroDecimal) { // cut decimal if it is equal to 0

			if ((isset($suffix)) && (strlen($suffix) > 0)) { // if need to add suffix

				// searching for fully formatted currency without currency symbol
				$options['display'] = \Zend_Currency::NO_SYMBOL;
				$answerBlank        = $this->_localizeNumber(parent::formatTxt($price, $options), $options);

				//print "answerBlank: " . $answerBlank . "<br />";
				// searching for fully formatted currency without currency symbol and rounded to int
				$options['precision'] = 0;
				$answerRound          = $this->_localizeNumber(parent::formatTxt($price, $this->_helper->clearOptions($options)), $options);
				//print "answerRound: " . $answerRound . "<br />";

				// replace cut decimals with suffix
				$answer = str_replace($answerBlank, $answerRound . $suffix, $answer);

				return $answer;
			} else { // only changing precision

				$options['precision'] = 0;
				$answer               = parent::formatTxt($price, $this->_helper->clearOptions($options));

				return $answer;
			}
		} else {

			return $answer;
		}
	}

	protected function _localizeNumber($number, $options = array())
	{
		$options = $this->_helper->getOptions($options, true, $this->getCurrencyCode());

		if ($options['display'] == \Zend_Currency::NO_SYMBOL) {

			// in Zend_Currency toCurrency() function
			// are stripped unbreakable spaces only for currency without Currency Symbol
			return $number;
		} else {

			$locale        = $this->_storeManager->getStore()->getLocaleCode();
			$format        = \Zend_Locale_Data::getContent($locale, 'decimalnumber');
			$numberOptions = array(
				'locale'        => $locale,
				'number_format' => $format,
				'precision'     => 0,
			);
			$number        = \Zend_Locale_Format::getNumber($number, $numberOptions);

			return \Zend_Locale_Format::toNumber($number, $numberOptions);
		}
	}

	//sometimes we need make correction
	public function convert($price, $toCurrency = null)
	{
		$result = parent::convert($price, $toCurrency);
		$data   = new DataObject(array(
			"price"      => $price,
			"toCurrency" => $toCurrency,
			"result"     => $result,
		));

		$this->_eventManager->dispatch("currency_convert_after", array("conversion" => $data));

		return $data->getData("result");
	}

	/*
	 * if Cut Zero Decimal = Yes
	 * formats number like this

	 * from 8.000 to 8
	 * from 8.100 to 8.1 or 8.10
	 * @return precision
	*/
	protected function getPrecisionToCutZeroDecimals($value, $minPrecision = 1)
	{
		$precision = 0;
		$possa     = 1;

		while ((float)($value * $possa) != (int)($value * $possa)) {

			//0.999999999999999 case
			$roundedOne = round(log10(abs((float)($value * $possa) - (int)($value * $possa))), 9);
			//for correction -0 float zero
			/*if($roundedOne == 0) {
				$roundedOne = 0;
			}*/
			//0.00000000000001 case
			$roundedZero = log10(abs((float)($value * $possa) - (int)($value * $possa)));

			if ($roundedZero < -9 || $roundedOne == 0) {

				break;
			}
			$possa *= 10;
			$precision++;

			//overflow error
			if ($precision > 29) {

				break;
			}
		}

		// TODO: WTF do not used $value?
		//$value = round($value, max(log10($possa), $minPrecision));
		return max($precision, $minPrecision);
	}

}