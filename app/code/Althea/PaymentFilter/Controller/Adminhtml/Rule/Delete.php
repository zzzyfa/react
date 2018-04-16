<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 14/09/2017
 * Time: 5:40 PM
 */

namespace Althea\PaymentFilter\Controller\Adminhtml\Rule;

use Althea\PaymentFilter\Controller\Adminhtml\Rule;

class Delete extends Rule {

	/**
	 * Delete payment filter rule action
	 *
	 * @return void
	 */
	public function execute()
	{
		$id = $this->getRequest()->getParam('rule_id');

		if ($id) {

			try {

				$model = $this->_objectManager->create('Althea\PaymentFilter\Model\Rule');

				$model->load($id);
				$model->delete();
				$this->messageManager->addSuccess(__('You deleted the rule.'));
				$this->_redirect('althea_paymentfilter/*/');

				return;
			} catch (\Magento\Framework\Exception\LocalizedException $e) {

				$this->messageManager->addError($e->getMessage());
			} catch (\Exception $e) {

				$this->messageManager->addError(__('We can\'t delete the rule right now. Please review the log and try again.'));
				$this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
				$this->_redirect('althea_paymentfilter/*/edit', ['rule_id' => $this->getRequest()->getParam('rule_id')]);

				return;
			}
		}
		
		$this->messageManager->addError(__('We can\'t find a rule to delete.'));
		$this->_redirect('althea_paymentfilter/*/');
	}

}