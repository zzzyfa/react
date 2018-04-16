<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 07/09/2017
 * Time: 4:59 PM
 */

namespace Althea\PaymentFilter\Block\Adminhtml\Rule\Edit;

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