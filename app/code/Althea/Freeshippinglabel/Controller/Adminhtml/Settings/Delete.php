<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 02/01/2018
 * Time: 3:47 PM
 */

namespace Althea\Freeshippinglabel\Controller\Adminhtml\Settings;

class Delete extends Index {

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
		$id = $this->getRequest()->getParam('id');

		if ($id) {

			try {

				// init model and delete
				$model = $this->_objectManager->create('Althea\Freeshippinglabel\Model\Label');

				$model->load($id);
				$model->delete();
				$this->messageManager->addSuccess(__('You deleted the label.')); // display success message

				// go to grid
				return $resultRedirect->setPath('*/*/');
			} catch (\Exception $e) {

				// display error message
				$this->messageManager->addError($e->getMessage());

				// go back to edit form
				return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
			}
		}

		// display error message
		$this->messageManager->addError(__('We can\'t find a label to delete.'));

		// go to grid
		return $resultRedirect->setPath('*/*/');
	}

}