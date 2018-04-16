<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 08/08/2017
 * Time: 10:27 AM
 */

namespace Althea\Cms\Block\Adminhtml\Alert\Edit;

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