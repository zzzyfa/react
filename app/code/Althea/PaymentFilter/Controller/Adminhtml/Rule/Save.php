<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 14/09/2017
 * Time: 12:19 PM
 */

namespace Althea\PaymentFilter\Controller\Adminhtml\Rule;

use Althea\PaymentFilter\Controller\Adminhtml\Rule;

class Save extends Rule {

	/**
	 * Payment filter rule save action
	 *
	 * @return void
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function execute()
	{
		if ($data = $this->getRequest()->getPostValue()) {

			try {
				/** @var $model \Althea\PaymentFilter\Model\Rule */
				$model = $this->_objectManager->create('Althea\PaymentFilter\Model\Rule');

				$this->_eventManager->dispatch('adminhtml_controller_althea_paymentfilter_prepare_save', [
					'request' => $this->getRequest(),
				]);

				$inputFilter = new \Zend_Filter_Input([], [], $data);
				$data        = $inputFilter->getUnescaped();
				$id          = $this->getRequest()->getParam('rule_id');

				if ($id) {

					$model->load($id);

					if ($id != $model->getId()) {

						throw new \Magento\Framework\Exception\LocalizedException(__('The wrong rule is specified.'));
					}
				}

				$session        = $this->_objectManager->get('Magento\Backend\Model\Session');
				$validateResult = $model->validateData(new \Magento\Framework\DataObject($data));

				if ($validateResult !== true) {

					foreach ($validateResult as $errorMessage) {

						$this->messageManager->addError($errorMessage);
					}

					$session->setPageData($data);
					$this->_redirect('althea_paymentfilter/*/edit', ['rule_id' => $model->getId()]);

					return;
				}

				if (isset($data['payment_method'])) {

					$data['payment_method'] = implode(",", $data['payment_method']);
				}

				if (isset($data['shipping_method'])) {

					$data['shipping_method'] = implode(",", $data['shipping_method']);
				}

				if (isset($data['rule']['conditions'])) {

					$data['conditions'] = $data['rule']['conditions'];
				}

				unset($data['rule']);

				$model->loadPost($data);
				$session->setPageData($model->getData());
				$model->save();
				$this->messageManager->addSuccess(__('You saved the rule.'));
				$session->setPageData(false);

				if ($this->getRequest()->getParam('back')) {

					$this->_redirect('althea_paymentfilter/*/edit', ['rule_id' => $model->getId()]);

					return;
				}

				$this->_redirect('althea_paymentfilter/*/');

				return;
			} catch (\Magento\Framework\Exception\LocalizedException $e) {

				$this->messageManager->addError($e->getMessage());

				$id = (int)$this->getRequest()->getParam('rule_id');

				if (!empty($id)) {

					$this->_redirect('althea_paymentfilter/*/edit', ['rule_id' => $id]);
				} else {

					$this->_redirect('althea_paymentfilter/*/new');
				}

				return;
			} catch (\Exception $e) {

				$this->messageManager->addError(
					__('Something went wrong while saving the rule data. Please review the error log.')
				);
				$this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
				$this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($data);
				$this->_redirect('althea_paymentfilter/*/edit', ['rule_id' => $this->getRequest()->getParam('rule_id')]);

				return;
			}
		}

		$this->_redirect('althea_paymentfilter/*/');
	}

}