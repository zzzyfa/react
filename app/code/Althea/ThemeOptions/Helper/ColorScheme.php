<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 30/01/2018
 * Time: 6:00 PM
 */

namespace Althea\ThemeOptions\Helper;

class ColorScheme extends \TemplateMonster\ThemeOptions\Helper\ColorScheme {

	const WEBSITE_CODE_DEFAULT = 'base';

	/**
	 * @inheritDoc
	 */
	protected function _getWebsiteCode($store = null)
	{
		return self::WEBSITE_CODE_DEFAULT;
	}

}