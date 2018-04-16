<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 29/12/2017
 * Time: 12:25 PM
 */

namespace Althea\Freeshippinglabel\Block\Adminhtml\Settings\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton extends GenericButton implements ButtonProviderInterface {

	/**
	 * @return array
	 */
	public function getButtonData()
	{
		$data = [];

		if ($this->getLabelId()) {

			$data = [
				'label'      => __('Delete Label'),
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
		return $this->getUrl('*/*/delete', ['id' => $this->getLabelId()]);
	}

}