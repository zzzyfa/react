<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 08/08/2017
 * Time: 10:28 AM
 */

namespace Althea\Cms\Block\Adminhtml\Alert\Edit;

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