<?php
/**
 * Copyright © 2017 Althea Sdn Bhd All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Althea\Customer\Model\Eav\Entity\Attribute\Source;

class Age extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource {

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
					"label" => __("<25"),
					"value" => 1,
				),
				array(
					"label" => __("25-34"),
					"value" => 2,
				),
				array(
					"label" => __("35-44"),
					"value" => 3,
				),
				array(
					"label" => __(">44"),
					"value" => 4,
				),
			);
		}

		return $this->_options;
	}

}