<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 28/12/2017
 * Time: 6:27 PM
 */

namespace Althea\Freeshippinglabel\Controller\Adminhtml\Settings;

use Aheadworks\Freeshippinglabel\Api\Data\LabelInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Edit extends Index {

	/**
	 * @inheritDoc
	 */
	public function execute()
	{
		$model = $this->_objectManager->create('\Aheadworks\Freeshippinglabel\Model\Label');

		if ($id = $this->getRequest()->getParam('id')) {

			try {

				$model = $this->_labelRepository->get($id);
			} catch (NoSuchEntityException $e) {

				$this->messageManager->addError(__('This label no longer exists.'));

				/** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
				$resultRedirect = $this->resultRedirectFactory->create();

				return $resultRedirect->setPath('*/*/');
			}
		}

		$this->_registerLabelContentData($model);

		/** @var \Magento\Backend\Model\View\Result\Page $resultPage */
		$resultPage = $this->_resultPageFactory->create();

		$this->_initPage($resultPage)
		     ->addBreadcrumb(
			     $id ? __('Edit Label') : __('New Label'),
			     $id ? __('Edit Label') : __('New Label')
		     );
		$resultPage->getConfig()
		           ->getTitle()
		           ->prepend(__('Labels'));
		$resultPage->getConfig()
		           ->getTitle()
		           ->prepend($model->getId() ? sprintf(__('Edit Label #%s'), $model->getId()) : __('New Label'));

		return $resultPage;
	}

	/**
	 * Register label content data
	 *
	 * @param LabelInterface $label
	 * @return void
	 */
	protected function _registerLabelContentData(LabelInterface $label)
	{
		$labelData        = $this->_dataPersistor->get('aw_fslabel_label')
			? $this->_dataPersistor->get('aw_fslabel_label')
			: $this->_dataObjectProcessor->buildOutputDataArray(
				$label,
				LabelInterface::class
			);
		$labelContentData = isset($labelData['content'])
			? $labelData['content']
			: [];

		if ($this->_dataPersistor->get('aw_fslabel_label')) {

			unset($labelData['content']);
			$this->_dataPersistor->set('aw_fslabel_label', $labelData);
		}

		$this->_coreRegistry->register('aw_fslabel_label_content', $labelContentData);
	}

}