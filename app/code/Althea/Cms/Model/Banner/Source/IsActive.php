<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 07/08/2017
 * Time: 5:26 PM
 */

namespace Althea\Cms\Model\Banner\Source;

use Althea\Cms\Model\Banner;
use Magento\Framework\Data\OptionSourceInterface;

class IsActive implements OptionSourceInterface {

	/**
	 * @var Banner;
	 */
	protected $banner;

	/**
	 * Constructor
	 *
	 * @param Banner $banner
	 */
	public function __construct(Banner $banner)
	{
		$this->banner = $banner;
	}

	/**
	 * Get options
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		$availableOptions = $this->banner->getAvailableStatuses();
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