<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 29/12/2017
 * Time: 12:24 PM
 */

namespace Althea\Freeshippinglabel\Block\Adminhtml\Settings\Edit;

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