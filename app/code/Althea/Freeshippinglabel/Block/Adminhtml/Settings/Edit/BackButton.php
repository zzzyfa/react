<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 29/12/2017
 * Time: 12:13 PM
 */

namespace Althea\Freeshippinglabel\Block\Adminhtml\Settings\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class BackButton extends GenericButton implements ButtonProviderInterface {

	/**
	 * @return array
	 */
	public function getButtonData()
	{
		return [
			'label'      => __('Back'),
			'on_click'   => sprintf("location.href = '%s';", $this->getBackUrl()),
			'class'      => 'back',
			'sort_order' => 10,
		];
	}

	/**
	 * Get URL for back (reset) button
	 *
	 * @return string
	 */
	public function getBackUrl()
	{
		return $this->getUrl('*/*/');
	}

}