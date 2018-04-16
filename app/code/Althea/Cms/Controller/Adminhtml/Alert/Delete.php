<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 09/08/2017
 * Time: 5:53 PM
 */

namespace Althea\Cms\Controller\Adminhtml\Alert;

use Althea\Cms\Controller\Adminhtml\Alert;

class Delete extends Alert {

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
		$id = $this->getRequest()->getParam('alert_id');

		if ($id) {

			try {

				// init model and delete
				$model = $this->_objectManager->create('Althea\Cms\Model\Alert');

				$model->load($id);
				$model->delete();
				$this->messageManager->addSuccess(__('You deleted the alert.')); // display success message

				// go to grid
				return $resultRedirect->setPath('*/*/');
			} catch (\Exception $e) {

				// display error message
				$this->messageManager->addError($e->getMessage());

				// go back to edit form
				return $resultRedirect->setPath('*/*/edit', ['alert_id' => $id]);
			}
		}

		// display error message
		$this->messageManager->addError(__('We can\'t find an alert to delete.'));

		// go to grid
		return $resultRedirect->setPath('*/*/');
	}

}