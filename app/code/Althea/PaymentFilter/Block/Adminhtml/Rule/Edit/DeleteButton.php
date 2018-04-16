<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 07/09/2017
 * Time: 5:01 PM
 */

namespace Althea\PaymentFilter\Block\Adminhtml\Rule\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton extends GenericButton implements ButtonProviderInterface {

	/**
	 * @return array
	 */
	public function getButtonData()
	{
		$data = [];

		if ($this->getRuleId()) {

			$data = [
				'label'      => __('Delete rule'),
				'class'      => 'delete',
				'on_click'   => 'deleteConfirm(\'' . __(
						'Are you sure you want to do this?'
					) . '\', \'' . $this->getDeleteUrl() . '\')',
				'sort_order' => 20,
			];
		}

		return $data;
	}

	/**
	 * @return string
	 */
	public function getDeleteUrl()
	{
		return $this->getUrl('*/*/delete', ['rule_id' => $this->getRuleId()]);
	}

}