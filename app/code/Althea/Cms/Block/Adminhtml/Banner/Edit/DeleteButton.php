<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 08/08/2017
 * Time: 10:20 AM
 */

namespace Althea\Cms\Block\Adminhtml\Banner\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton extends GenericButton implements ButtonProviderInterface {

	/**
	 * @return array
	 */
	public function getButtonData()
	{
		$data = [];

		if ($this->getBannerId()) {

			$data = [
				'label'      => __('Delete Banner'),
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
		return $this->getUrl('*/*/delete', ['banner_id' => $this->getBannerId()]);
	}

}