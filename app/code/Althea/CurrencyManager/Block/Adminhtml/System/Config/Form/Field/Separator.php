<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 15/08/2017
 * Time: 5:25 PM
 */

namespace Althea\CurrencyManager\Block\Adminhtml\System\Config\Form\Field;


use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Separator extends Field {

	/**
	 * render separator config row
	 *
	 * @param AbstractElement $element
	 * @return string
	 */
	public function render(AbstractElement $element)
	{
		$fieldConfig = $element->getFieldConfig();
		$htmlId      = $element->getHtmlId();
		$html        = '<tr id="row_' . $htmlId . '">'
			. '<td class="label" colspan="3">';
//        $marginTop = '0px';
//        if(isset($fieldConfig['margin_top']) && $fieldConfig['margin_top']){
//            $marginTop = $fieldConfig['margin_top'];
//        }
		$customStyle = '';

		if (isset($fieldConfig['style']) && $fieldConfig['style']) {

			$customStyle = $fieldConfig['style'];
		}

		$html .= '<div style="margin-top:10px; font-weight: bold; border-bottom: 1px solid #dfdfdf;text-align:left;'
			. $customStyle . '">';
		$html .= $element->getLabel();
		$html .= '</div></td></tr>';

		return $html;
	}

}