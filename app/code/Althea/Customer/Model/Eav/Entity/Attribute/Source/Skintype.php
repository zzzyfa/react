<?php
/**
 * Copyright © 2017 Althea Sdn Bhd All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Althea\Customer\Model\Eav\Entity\Attribute\Source;

class Skintype extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource {

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
					"label" => "Normal",
					"value" => 1,
				),
				array(
					"label" => "Dry",
					"value" => 2,
				),
				array(
					"label" => "Oily",
					"value" => 3,
				),
				array(
					"label" => "Combination",
					"value" => 4,
				),
			);
		}

		return $this->_options;
	}

}