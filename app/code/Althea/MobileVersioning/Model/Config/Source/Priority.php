<?php
/**
 * Created by PhpStorm.
 * User: manadirmahi
 * Date: 29/09/2017
 * Time: 12:29 PM
 */

namespace Althea\MobileVersioning\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Priority implements ArrayInterface {

	/**
	 * Options getter
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		return [
			[
				'value' => 1,
				'label' => __('Lowest'),
			],
			[
				'value' => 2,
				'label' => __('Low'),
			],
			[
				'value' => 3,
				'label' => __('Medium'),
			],
			[
				'value' => 4,
				'label' => __('High'),
			],
			[
				'value' => 5,
				'label' => __('Highest'),
			],
		];
	}

	/**
	 * Get options in "key-value" format
	 *
	 * @return array
	 */
	public function toArray()
	{
		return [
			1 => __('Lowest'),
			2 => __('Low'),
			3 => __('Medium'),
			4 => __('High'),
			5 => __('Highest'),
		];
	}
}