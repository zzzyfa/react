<?php
/**
 * Created by PhpStorm.
 * User: manadirmahi
 * Date: 29/09/2017
 * Time: 3:45 PM
 */

namespace Althea\MobileVersioning\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Config\Block\System\Config\Form\Field;

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