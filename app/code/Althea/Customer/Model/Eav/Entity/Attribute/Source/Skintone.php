<?php
/**
 * Copyright © 2017 Althea Sdn Bhd All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Althea\Customer\Model\Eav\Entity\Attribute\Source;

class Skintone extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource {

	/**
	 * Retrieve All options
	 *
	 * @return array
	 */
	public function getAllOptions()
	{
		if (is_null($this->_options)) {

			$this->_options = array(
				array(
					"label" => __("Light"),
					"value" => 1,
				),
				array(
					"label" => __("Medium"),
					"value" => 2,
				),
				array(
					"label" => __("Dark"),
					"value" => 3,
				),
			);
		}

		return $this->_options;
	}

}