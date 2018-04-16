<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 30/01/2018
 * Time: 6:30 PM
 */

namespace Althea\ThemeOptions\Block\Adminhtml\System\Config\Field;

class ColorScheme extends \TemplateMonster\ThemeOptions\Block\Adminhtml\System\Config\Field\ColorScheme {

	/**
	 * @inheritDoc
	 */
	protected function _getWebsiteCode()
	{
		return \Althea\ThemeOptions\Helper\ColorScheme::WEBSITE_CODE_DEFAULT;
	}

}