<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 15/08/2017
 * Time: 6:52 PM
 */

namespace Althea\CurrencyManager\Block\Adminhtml;

use Althea\CurrencyManager\Helper\Config;
use Althea\CurrencyManager\Model\Config\Source\TypePosition;
use Althea\CurrencyManager\Model\Config\Source\TypeSymbolUse;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Framework\Locale\ConfigInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Locale\TranslatedLists;

class SymbolReplace extends Field {

	const FIELD_PRECISION             = 'precision';
	const FIELD_MIN_DECIMAL_COUNT     = 'min_decimal_count';
	const FIELD_CUTZERODECIMAL        = 'cutzerodecimal';
	const FIELD_CUTZERODECIMAL_SUFFIX = 'cutzerodecimal_suffix';
	const FIELD_POSITION              = 'position';
	const FIELD_DISPLAY               = 'display';
	const FIELD_SYMBOL                = 'symbol';
	const FIELD_ZEROTEXT              = 'zerotext';

	protected $_addRowButtonHtml    = array();
	protected $_removeRowButtonHtml = array();
	protected $_listsModel;
	protected $_currencyFactory;
	protected $_configHelper;
	protected $_sourceYesNo;
	protected $_sourceTypePosition;
	protected $_sourceTypeSymbolUse;

	/**
	 * SymbolReplace constructor.
	 */
	public function __construct(
		ConfigInterface $configInterface,
		ResolverInterface $resolverInterface,
		CurrencyFactory $currencyFactory,
		Config $configHelper,
		Yesno $sourceYesNo,
		TypePosition $sourceTypePosition,
		TypeSymbolUse $sourceTypeSymbolUse,
		Context $context,
		array $data = []
	)
	{
		$this->_listsModel          = new TranslatedLists($configInterface, $resolverInterface);
		$this->_currencyFactory     = $currencyFactory;
		$this->_configHelper        = $configHelper;
		$this->_sourceYesNo         = $sourceYesNo;
		$this->_sourceTypePosition  = $sourceTypePosition;
		$this->_sourceTypeSymbolUse = $sourceTypeSymbolUse;

		parent::__construct($context, $data);
	}

	protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
	{
		$this->setElement($element);

		$html = '<div id="symbolreplace_template" style="display: none">';
		$html .= $this->_getRowTemplateHtml();
		$html .= '</div>';
		$html .= '<ul id="symbolreplace_container" style="list-style: none;">';

		if ($this->_getValue('currency')) {

			foreach (array_keys($this->_getValue('currency')) as $row) {

				if ($row) {

					$html .= $this->_getRowTemplateHtml($row);
				}
			}
		}

		$html .= '</ul>';
		$html .= $this->_getAddRowButtonHtml('symbolreplace_container', 'symbolreplace_template', __('Add currency specific options'));

		return $html;
	}

	protected function _getRowTemplateHtml($row = 0)
	{
		$html = $this->_getCurrencySelectHtml($row);
		$html .= $this->_getPrecisionHtml($row);
		$html .= $this->_getMinDecimalCountHtml($row);
		$html .= $this->_getCutZeroHtml($row);
		$html .= $this->_getSuffixHtml($row);
		$html .= $this->_getSymbolPositionHtml($row);
		$html .= $this->_getSymbolUseHtml($row);
		$html .= $this->_getSymbolReplaceHtml($row);
		$html .= $this->_getZeroPriceReplaceHtml($row);

		return sprintf('
			<li>
				<fieldset>
					<table cellspacing="0">%s</table>
					<br /><br />%s
				</fieldset>
			</li>
		', $html, $this->_getRemoveRowButtonHtml());
	}

	protected function _getZeroPriceReplaceHtml($row)
	{
		$label = __('Replace Zero Price to');
		$field = sprintf('
			<input class="input-text admin__control-text" name="%s[zerotext][]" value="%s" %s/>
			<p class="note">
				<span>%s</span>
			</p>
		', $this->getElement()->getName(), $this->_getValue('zerotext/' . $row), $this->_getDisabled(), __('Leave empty for global value use'));
		$html  = $this->_generateTableFormField($label, $field);

		return $html;
	}

	protected function _getSymbolReplaceHtml($row)
	{
		$label = __('Replace symbol to');
		$field = sprintf('
			<input class="input-text admin__control-text" name="%s[symbol][]" value="%s" %s/>
			<p class="note">
				<span>%s</span>
			</p>
		', $this->getElement()->getName(), $this->_getValue('symbol/' . $row), $this->_getDisabled(), __('Leave empty for disable replace'));
		$html  = $this->_generateTableFormField($label, $field);

		return $html;
	}

	protected function _getSymbolUseHtml($row)
	{
		$label   = __('Currency symbol use');
		$options = "";

		foreach ($this->_sourceTypeSymbolUse->toOptionArray() as $labelValue) {

			$options .= sprintf('
				<option value="%s" %s>%s</option>
			', $labelValue['value'], ($this->_getValue('display/' . $row) == $labelValue['value'] ? 'selected="selected"' : ''), $labelValue['label']);
		}

		$field = sprintf('
			<select class="input-text admin__control-text" name="%s[display][]">%s</select>
		', $this->getElement()->getName(), $options);
		$html  = $this->_generateTableFormField($label, $field);

		return $html;
	}

	protected function _getSymbolPositionHtml($row)
	{
		$label   = __('Symbol position');
		$options = "";

		foreach ($this->_sourceTypePosition->toOptionArray() as $labelValue) {

			$options .= sprintf('
				<option value="%s" %s>%s</option>
			', $labelValue['value'], ($this->_getValue('position/' . $row) == $labelValue['value'] ? 'selected="selected"' : ''), $labelValue['label']);
		}

		$field = sprintf('
			<select class="input-text admin__control-text" name="%s[position][]">%s</select>
		', $this->getElement()->getName(), $options);
		$html  = $this->_generateTableFormField($label, $field);

		return $html;
	}

	protected function _getSuffixHtml($row)
	{
		$label = __('Suffix');
		$field = sprintf('
			<input class="input-text admin__control-text" name="%s[cutzerodecimal_suffix][]" value="%s" %s/>
			<p class="note">
				<span>%s</span>
			</p>
		', $this->getElement()->getName(), $this->_getValue('cutzerodecimal_suffix/' . $row), $this->_getDisabled(), __('Leave empty for global value use'));
		$html  = $this->_generateTableFormField($label, $field);

		return $html;
	}

	protected function _getMinDecimalCountHtml($row)
	{
		$label = __('Minimum number of digits after the decimal point');
		$field = sprintf('
			<input class="input-text admin__control-text" name="%s[min_decimal_count][]" value="%s" %s/>
			<p class="note">
				<span>%s</span>
			</p>
		', $this->getElement()->getName(), $this->_getValue('min_decimal_count/' . $row), $this->_getDisabled(), __('Leave empty for global value use'));
		$html  = $this->_generateTableFormField($label, $field);

		return $html;
	}

	protected function _getCutZeroHtml($row)
	{
		$label   = __('Cut Zero Decimals');
		$options = "";

		foreach ($this->_sourceYesNo->toOptionArray() as $labelValue) {

			$options .= sprintf('
				<option value="%s" style="background: white;" %s>%s</option>
			', $labelValue['value'], ($this->_getValue('cutzerodecimal/' . $row) == $labelValue["value"] ? 'selected="selected"' : ''), $labelValue['label']);
		}

		$field = sprintf('
			<select class="input-text admin__control-text" name="%s[cutzerodecimal][]">%s</select>
		', $this->getElement()->getName(), $options);
		$html  = $this->_generateTableFormField($label, $field);

		return $html;
	}

	protected function _getPrecisionHtml($row)
	{
		$label = __('Display precision');
		$field = sprintf('
			<input class="input-text admin__control-text" name="%s[precision][]" value="%s" %s/>
			<p class="note">
				<span>%s</span>
			</p>
		', $this->getElement()->getName(), $this->_getValue('precision/' . $row), $this->_getDisabled(), __('Leave empty for global value use'));
		$html  = $this->_generateTableFormField($label, $field);

		return $html;
	}

	protected function _getCurrencySelectHtml($row)
	{
		$label   = __('Select currency');
		$options = "";

		foreach ($this->getAllowedCurrencies() as $currencyCode => $currency) {

			$options .= sprintf('
				<option value="%s" %s style="background: white;">%s-%s</option>
			', $currencyCode, $this->_getSelected('currency/' . $row, $currencyCode), $currency, $currencyCode);
		}

		$field = sprintf('
			<select name="%s[currency][]" %s>
				<option value="">%s</option>%s
			</select>
		', $this->getElement()->getName(), $this->_getDisabled(), __('* Select currency'), $options);

		$html = $this->_generateTableFormField($label, $field);

		return $html;
	}

	protected function getAllowedCurrencies()
	{

		if (!$this->hasData('allowed_currencies')) {

			$currencies           = $this->_listsModel->getOptionCurrencies();
			$currencyModel        = $this->_currencyFactory->create();
			$allowedCurrencyCodes = $currencyModel->getConfigAllowCurrencies();
			$formattedCurrencies  = array();

			foreach ($currencies as $currency) {

				$formattedCurrencies[$currency['value']]['label'] = $currency['label'];
			}

			$allowedCurrencies = array();

			foreach ($allowedCurrencyCodes as $currencyCode) {

				$allowedCurrencies[$currencyCode] = $formattedCurrencies[$currencyCode]['label'];
			}

			$this->setData('allowed_currencies', $allowedCurrencies);
		}

		return $this->getData('allowed_currencies');
	}

	protected function _getDisabled()
	{
		return $this->getElement()->getDisabled() ? ' disabled' : '';
	}

	protected function _getValue($key)
	{
		$value = $this->getElement()->getData('value/' . $key);

		if (is_null($value) && $key != 'currency') {

			$key = explode("/", $key);
			$key = array_shift($key);
			//$value = Mage::app()->getConfig()->getNode('default/currencymanager/general/symbolreplace/'.$key);
//			$value = Mage::app()->getConfig()->getNode('default/currencymanager/general/' . $key);

			switch ($key) {

				case self::FIELD_PRECISION:
					$value = $this->_configHelper->getGeneralPrecision();
					break;
				case self::FIELD_MIN_DECIMAL_COUNT:
					$value = $this->_configHelper->getGeneralMinDecimalCount();
					break;
				case self::FIELD_CUTZERODECIMAL:
					$value = $this->_configHelper->getGeneralCutZeroDecimal();
					break;
				case self::FIELD_CUTZERODECIMAL_SUFFIX:
					$value = $this->_configHelper->getGeneralCutZeroDecimalSuffix();
					break;
				case self::FIELD_POSITION:
					$value = $this->_configHelper->getGeneralPosition();
					break;
				case self::FIELD_DISPLAY:
					$value = $this->_configHelper->getGeneralDisplay();
					break;
				case self::FIELD_SYMBOL:
					$value = $this->_configHelper->getGeneralSymbol();
					break;
				case self::FIELD_ZEROTEXT:
					$value = $this->_configHelper->getGeneralZeroText();
					break;
			}

			return (string)$value;
		}

		return $value;
	}

	protected function _getSelected($key, $value)
	{
		return $this->getElement()->getData('value/' . $key) == $value ? 'selected="selected"' : '';
	}

	protected function _getAddRowButtonHtml($container, $template, $title = 'Add')
	{
		if (!isset($this->_addRowButtonHtml[$container])) {

			$this->_addRowButtonHtml[$container] = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
			                                            ->setType('button')
			                                            ->setClass('add ' . $this->_getDisabled())
			                                            ->setLabel(__($title))
			                                            ->setOnClick("Element.insert($('" . $container . "'), {bottom: $('" . $template . "').innerHTML})")
			                                            ->setDisabled($this->_getDisabled())
			                                            ->toHtml();
		}

		return $this->_addRowButtonHtml[$container];
	}

	protected function _getRemoveRowButtonHtml($selector = 'li', $title = 'Remove')
	{
		if (!$this->_removeRowButtonHtml) {

			$this->_removeRowButtonHtml = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
			                                   ->setType('button')
			                                   ->setClass('delete v-middle ' . $this->_getDisabled())
			                                   ->setLabel(__($title))
			                                   ->setOnClick("Element.remove($(this).up('" . $selector . "'))")
			                                   ->setDisabled($this->_getDisabled())
			                                   ->toHtml();
		}

		return $this->_removeRowButtonHtml;
	}

	protected function _generateTableFormField($label, $field)
	{
		return sprintf('
			<tr>
				<td>
					<label>
						<span>%s</span>
					</label>
				</td>
				<td>%s</td>
			</tr>
		', $label, $field);
	}

}