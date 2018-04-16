<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 22/03/2018
 * Time: 3:16 PM
 */

namespace Althea\CatalogSearch\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class Mapping
 *
 * - based on Smile-SA/elasticsuite for reference
 *
 * @package Althea\CatalogSearch\Helper
 */
class Mapping extends AbstractHelper {

	/**
	 * @var string
	 */
	const OPTION_TEXT_PREFIX = 'option_text';

	/**
	 * Transform a field name into it's option value field form.
	 *
	 * @param string $fieldName The field name to be converted.
	 *
	 * @return string
	 */
	public function getOptionTextFieldName($fieldName)
	{
		return sprintf("%s_%s", self::OPTION_TEXT_PREFIX, $fieldName);
	}

}