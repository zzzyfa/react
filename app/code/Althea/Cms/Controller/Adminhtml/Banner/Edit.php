<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 08/08/2017
 * Time: 9:58 AM
 */

namespace Althea\Cms\Controller\Adminhtml\Banner;

use Althea\Cms\Controller\Adminhtml\Banner;

class Edit extends Banner {

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
	 * Edit CMS banner
	 *
	 * @return \Magento\Framework\Controller\ResultInterface
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function execute()
	{
		// 1. Get ID and create model
		$id    = $this->getRequest()->getParam('banner_id');
		$model = $this->_objectManager->create('Althea\Cms\Model\Banner');

		// 2. Initial checking
		if ($id) {

			$model->load($id);

			if (!$model->getId()) {

				$this->messageManager->addError(__('This banner no longer exists.'));

				/** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
				$resultRedirect = $this->resultRedirectFactory->create();

				return $resultRedirect->setPath('*/*/');
			}
		}

		$this->_coreRegistry->register('cms_banner', $model);

		// 5. Build edit form
		/** @var \Magento\Backend\Model\View\Result\Page $resultPage */
		$resultPage = $this->resultPageFactory->create();

		$this->initPage($resultPage)->addBreadcrumb(
			$id ? __('Edit Banner') : __('New Banner'),
			$id ? __('Edit Banner') : __('New Banner')
		);
		$resultPage->getConfig()->getTitle()->prepend(__('Banners'));
		$resultPage->getConfig()->getTitle()->prepend($model->getId() ? $model->getTitle() : __('New Banner'));

		return $resultPage;
	}

}