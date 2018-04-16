<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 07/09/2017
 * Time: 4:23 PM
 */

namespace Althea\PaymentFilter\Model\Rule\Source;

use Althea\PaymentFilter\Model\Rule;
use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface {

	/**
	 * @var Rule;
	 */
	protected $rule;

	/**
	 * Constructor
	 *
	 * @param Rule $rule
	 */
	public function __construct(Rule $rule)
	{
		$this->rule = $rule;
	}

	/**
	 * Get options
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		$availableOptions = $this->rule->getAvailableStatuses();
		$options          = [];

		foreach ($availableOptions as $key => $value) {

			$options[] = [
				'label' => $value,
				'value' => $key,
			];
		}

		return $options;
	}

}