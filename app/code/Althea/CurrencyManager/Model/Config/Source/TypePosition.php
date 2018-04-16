<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 15/08/2017
 * Time: 5:58 PM
 */

namespace Althea\CurrencyManager\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class TypePosition implements ArrayInterface {

	/**
	 * Options getter
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		return [
			[
				'value' => 8,
				'label' => __('Default'),
			],
			[
				'value' => 16,
				'label' => __('Right'),
			],
			[
				'value' => 32,
				'label' => __('Left'),
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
			8  => __('Default'),
			16 => __('Right'),
			32 => __('Left'),
		];
	}

}