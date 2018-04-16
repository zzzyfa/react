<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 15/08/2017
 * Time: 6:07 PM
 */

namespace Althea\CurrencyManager\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class TypeSymbolUse implements ArrayInterface {

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
				'label' => __('Do not use'),
			],
			[
				'value' => 2,
				'label' => __('Use symbol'),
			],
			[
				'value' => 3,
				'label' => __('Use short name'),
			],
			[
				'value' => 4,
				'label' => __('Use name'),
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
			1 => __('Do not use'),
			2 => __('Use symbol'),
			3 => __('Use short name'),
			4 => __('Use name'),
		];
	}

}