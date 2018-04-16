<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 07/09/2017
 * Time: 5:49 PM
 */

namespace Althea\PaymentFilter\Block\Adminhtml\Rule\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class SaveAndContinueButton extends GenericButton implements ButtonProviderInterface {

	/**
	 * @return array
	 */
	public function getButtonData()
	{
		return [
			'label'          => __('Save and Continue Edit'),
			'class'          => 'save',
			'data_attribute' => [
				'mage-init' => [
					'button' => ['event' => 'saveAndContinueEdit'],
				],
			],
			'sort_order'     => 80,
		];
	}

}