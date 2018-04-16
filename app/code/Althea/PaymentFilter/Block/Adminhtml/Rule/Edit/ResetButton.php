<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 07/09/2017
 * Time: 5:47 PM
 */

namespace Althea\PaymentFilter\Block\Adminhtml\Rule\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class ResetButton extends GenericButton implements ButtonProviderInterface {

	/**
	 * @return array
	 */
	public function getButtonData()
	{
		return [
			'label'      => __('Reset'),
			'class'      => 'reset',
			'on_click'   => 'location.reload();',
			'sort_order' => 30,
		];
	}

}