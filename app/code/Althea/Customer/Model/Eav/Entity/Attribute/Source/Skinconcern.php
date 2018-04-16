<?php
/**
 * Copyright © 2017 Althea Sdn Bhd All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Althea\Customer\Model\Eav\Entity\Attribute\Source;

class Skinconcern extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource {

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
					"label" => __("Acne"),
					"value" => 1,
				),
				array(
					"label" => __("Aging"),
					"value" => 2,
				),
				array(
					"label" => __("Allergies"),
					"value" => 3,
				),
				array(
					"label" => __("Dryness"),
					"value" => 4,
				),
				array(
					"label" => __("Pores"),
					"value" => 5,
				),
				array(
					"label" => __("Sensitive skin"),
					"value" => 6,
				),
				array(
					"label" => __("Skin tone/texture"),
					"value" => 7,
				),
			);
		}

		return $this->_options;
	}

}