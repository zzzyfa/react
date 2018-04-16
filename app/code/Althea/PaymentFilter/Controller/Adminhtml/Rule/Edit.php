<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 07/09/2017
 * Time: 4:47 PM
 */

namespace Althea\PaymentFilter\Controller\Adminhtml\Rule;

use Althea\PaymentFilter\Controller\Adminhtml\Rule;

class Edit extends Rule {

	/**
	 * @var \Magento\Framework\View\Result\PageFactory
	 */
	protected $resultPageFactory;

	/**
	 * @param \Magento\Backend\App\Action\Context        $context
	 * @param \Magento\Framework\Registry                $coreRegistry
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
	 */
	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\Registry $coreRegistry,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory
	)
	{
		$this->resultPageFactory = $resultPageFactory;

		parent::__construct($context, $coreRegistry);
	}

	/**
	 * Edit payment filter rule
	 *
	 * @return \Magento\Framework\Controller\ResultInterface
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function execute()
	{
		// 1. Get ID and create model
		$id    = $this->getRequest()->getParam('rule_id');
		$model = $this->_objectManager->create('Althea\PaymentFilter\Model\Rule');

		// 2. Initial checking
		if ($id) {

			$model->load($id);

			if (!$model->getId()) {

				$this->messageManager->addError(__('This rule no longer exists.'));

				/** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
				$resultRedirect = $this->resultRedirectFactory->create();

				return $resultRedirect->setPath('*/*/');
			}
		}

		$this->_coreRegistry->register('paymentfilter_rule', $model);

		// 5. Build edit form
		/** @var \Magento\Backend\Model\View\Result\Page $resultPage */
		$resultPage = $this->resultPageFactory->create();

		if ($id) {

			$model->load($id);

			if (!$model->getRuleId()) {

				$this->messageManager->addError(__('This rule no longer exists.'));
				$this->_redirect('althea_paymentfilter/*');

				return;
			}

			$model->getConditions()->setFormName('althea_paymentfilter_rule_form');
			$model->getConditions()->setJsFormObject($model->getConditionsFieldSetId($model->getConditions()->getFormName()));
			$model->getActions()->setFormName('althea_paymentfilter_rule_form');
			$model->getActions()->setJsFormObject($model->getActionsFieldSetId($model->getActions()->getFormName()));
		}

		// set entered data if was error when we do save
		$data = $this->_objectManager->get('Magento\Backend\Model\Session')
		                             ->getPageData(true);

		if (!empty($data)) {

			$model->addData($data);
		}

		$this->initPage($resultPage)->addBreadcrumb(
			$id ? __('Edit Rule') : __('New Rule'),
			$id ? __('Edit Rule') : __('New Rule')
		);
		$resultPage->getConfig()->getTitle()->prepend(__('Rules'));
		$resultPage->getConfig()->getTitle()->prepend($model->getId() ? $model->getTitle() : __('New Rule'));

		return $resultPage;
	}

}