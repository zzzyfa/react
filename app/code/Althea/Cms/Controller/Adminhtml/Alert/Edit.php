<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 08/08/2017
 * Time: 9:58 AM
 */

namespace Althea\Cms\Controller\Adminhtml\Alert;

use Althea\Cms\Controller\Adminhtml\Alert;

class Edit extends Alert {

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
	 * Edit CMS alert
	 *
	 * @return \Magento\Framework\Controller\ResultInterface
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function execute()
	{
		// 1. Get ID and create model
		$id    = $this->getRequest()->getParam('alert_id');
		$model = $this->_objectManager->create('Althea\Cms\Model\Alert');

		// 2. Initial checking
		if ($id) {

			$model->load($id);

			if (!$model->getId()) {

				$this->messageManager->addError(__('This alert no longer exists.'));

				/** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
				$resultRedirect = $this->resultRedirectFactory->create();

				return $resultRedirect->setPath('*/*/');
			}
		}

		$this->_coreRegistry->register('cms_alert', $model);

		// 5. Build edit form
		/** @var \Magento\Backend\Model\View\Result\Page $resultPage */
		$resultPage = $this->resultPageFactory->create();

		$this->initPage($resultPage)->addBreadcrumb(
			$id ? __('Edit Alert') : __('New Alert'),
			$id ? __('Edit Alert') : __('New Alert')
		);
		$resultPage->getConfig()->getTitle()->prepend(__('Alerts'));
		$resultPage->getConfig()->getTitle()->prepend($model->getId() ? $model->getTitle() : __('New Alert'));

		return $resultPage;
	}

}