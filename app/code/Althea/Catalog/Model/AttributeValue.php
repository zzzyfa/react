<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 14/03/2018
 * Time: 4:21 PM
 */

namespace Althea\Catalog\Model;

use Althea\Catalog\Api\Data\AttributeInterface;

class AttributeValue extends \Magento\Framework\Api\AttributeValue implements AttributeInterface {

	/**
	 * @inheritDoc
	 */
	public function getFrontendLabel()
	{
		return $this->_get(self::FRONTEND_LABEL);
	}

	/**
	 * @inheritDoc
	 */
	public function setFrontendLabel($label)
	{
		$this->_data[self::FRONTEND_LABEL] = $label;

		return $this;
	}

}