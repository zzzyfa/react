<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 09/08/2017
 * Time: 5:53 PM
 */

namespace Althea\Cms\Controller\Adminhtml\Banner;

use Althea\Cms\Controller\Adminhtml\Banner;

class Delete extends Banner {

	/**
	 * Delete action
	 *
	 * @return \Magento\Framework\Controller\ResultInterface
	 */
	public function execute()
	{
		/** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
		$resultRedirect = $this->resultRedirectFactory->create();

		// check if we know what should be deleted
		$id = $this->getRequest()->getParam('banner_id');

		if ($id) {

			try {

				// init model and delete
				$model = $this->_objectManager->create('Althea\Cms\Model\Banner');

				$model->load($id);
				$model->delete();
				$this->messageManager->addSuccess(__('You deleted the banner.')); // display success message

				// go to grid
				return $resultRedirect->setPath('*/*/');
			} catch (\Exception $e) {

				// display error message
				$this->messageManager->addError($e->getMessage());

				// go back to edit form
				return $resultRedirect->setPath('*/*/edit', ['banner_id' => $id]);
			}
		}

		// display error message
		$this->messageManager->addError(__('We can\'t find a banner to delete.'));

		// go to grid
		return $resultRedirect->setPath('*/*/');
	}

}