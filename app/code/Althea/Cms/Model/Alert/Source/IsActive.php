<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 07/08/2017
 * Time: 5:26 PM
 */

namespace Althea\Cms\Model\Alert\Source;

use Althea\Cms\Model\Alert;
use Magento\Framework\Data\OptionSourceInterface;

class IsActive implements OptionSourceInterface {

	/**
	 * @var Alert;
	 */
	protected $alert;

	/**
	 * Constructor
	 *
	 * @param Alert $alert
	 */
	public function __construct(Alert $alert)
	{
		$this->alert = $alert;
	}

	/**
	 * Get options
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		$availableOptions = $this->alert->getAvailableStatuses();
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