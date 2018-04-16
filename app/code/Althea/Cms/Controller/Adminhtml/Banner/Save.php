<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 09/08/2017
 * Time: 3:04 PM
 */

namespace Althea\Cms\Controller\Adminhtml\Banner;

use Althea\Cms\Controller\Adminhtml\Banner;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends Banner {

	/**
	 * @var DataPersistorInterface
	 */
	protected $dataPersistor;

	/**
	 * @param Context                     $context
	 * @param \Magento\Framework\Registry $coreRegistry
	 * @param DataPersistorInterface      $dataPersistor
	 */
	public function __construct(
		Context $context,
		\Magento\Framework\Registry $coreRegistry,
		DataPersistorInterface $dataPersistor
	)
	{
		$this->dataPersistor = $dataPersistor;

		parent::__construct($context, $coreRegistry);
	}

	/**
	 * Save action
	 *
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 * @return \Magento\Framework\Controller\ResultInterface
	 */
	public function execute()
	{
		/** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
		$resultRedirect = $this->resultRedirectFactory->create();
		$data           = $this->getRequest()->getPostValue();

		if ($data) {

			$id = $this->getRequest()->getParam('banner_id');

			if (isset($data['is_active']) && $data['is_active'] === 'true') {

				$data['is_active'] = \Althea\Cms\Model\Banner::STATUS_ENABLED;
			}

			if (empty($data['banner_id'])) {

				$data['banner_id'] = null;
			}

			/** @var \Althea\Cms\Model\Banner $model */
			$model = $this->_objectManager->create('Althea\Cms\Model\Banner')->load($id);

			if (!$model->getId() && $id) {

				$this->messageManager->addError(__('This banner no longer exists.'));

				return $resultRedirect->setPath('*/*/');
			}

			$model->setData($data);

			try {

				$model->save();
				$this->messageManager->addSuccess(__('You saved the banner.'));
				$this->dataPersistor->clear('cms_banner');

				if ($this->getRequest()->getParam('back')) {

					return $resultRedirect->setPath('*/*/edit', ['banner_id' => $model->getId()]);
				}

				return $resultRedirect->setPath('*/*/');
			} catch (LocalizedException $e) {

				$this->messageManager->addError($e->getMessage());
			} catch (\Exception $e) {

				$this->messageManager->addException($e, __('Something went wrong while saving the banner.'));
			}

			$this->dataPersistor->set('cms_banner', $data);

			return $resultRedirect->setPath('*/*/edit', ['banner_id' => $this->getRequest()->getParam('banner_id')]);
		}

		return $resultRedirect->setPath('*/*/');
	}

}