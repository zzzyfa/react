<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 02/01/2018
 * Time: 12:56 PM
 */

namespace Althea\Freeshippinglabel\Controller\Adminhtml\Settings;

use Aheadworks\Freeshippinglabel\Api\Data\LabelInterfaceFactory;
use Aheadworks\Freeshippinglabel\Api\LabelRepositoryInterface;
use Althea\Freeshippinglabel\Api\Data\LabelInterface;
use Althea\Freeshippinglabel\Model\LabelFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Request\DataPersistorInterface;

class Save extends \Aheadworks\Freeshippinglabel\Controller\Adminhtml\Settings\Save {

	protected $_labelRepository;
	protected $_dataObjectHelper;
	protected $_dataPersistor;
	protected $_labelFactory;

	/**
	 * @inheritDoc
	 */
	public function __construct(
		Context $context,
		LabelFactory $labelFactory,
		LabelRepositoryInterface $labelRepository,
		LabelInterfaceFactory $labelDataFactory,
		DataObjectHelper $dataObjectHelper,
		DataPersistorInterface $dataPersistor
	)
	{
		$this->_labelRepository  = $labelRepository;
		$this->_dataObjectHelper = $dataObjectHelper;
		$this->_dataPersistor    = $dataPersistor;
		$this->_labelFactory     = $labelFactory;

		parent::__construct($context, $labelRepository, $labelDataFactory, $dataObjectHelper, $dataPersistor);
	}

	/**
	 * @inheritDoc
	 */
	public function execute()
	{
		$data = $this->_prepareData($this->getRequest()->getPostValue());

		/** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
		$resultRedirect = $this->resultRedirectFactory->create();

		if ($data) {

			try {

				// althea: create new label / edit existing label
				$labelDataObject = $this->_objectManager->create('Althea\Freeshippinglabel\Model\Label')
				                                        ->load($this->getRequest()->getParam('id'));

				if (empty($data['id'])) {

					$data['id'] = null;
				}

				$this->_dataObjectHelper->populateWithArray(
					$labelDataObject,
					$data,
					LabelInterface::class // althea: changed to custom label interface
				);

				$this->_labelRepository->save($labelDataObject);
				$this->_dataPersistor->clear('aw_fslabel_label');
				$this->messageManager->addSuccessMessage(__('Settings were successfully saved'));

				if ($this->getRequest()->getParam('back')) {

					return $resultRedirect->setPath('*/*/edit', ['id' => $labelDataObject->getId()]);
				}

				return $resultRedirect->setPath('*/*/');
			} catch (\Magento\Framework\Exception\LocalizedException $e) {

				$this->messageManager->addErrorMessage($e->getMessage());
			} catch (\RuntimeException $e) {

				$this->messageManager->addErrorMessage($e->getMessage());
			} catch (\Exception $e) {

				$this->messageManager->addExceptionMessage($e, __('Something went wrong while saving label settings'));
			}

			$this->_dataPersistor->set('aw_fslabel_label', $data);
		}

		return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
	}

	/**
	 * Prepare post data
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	protected function _prepareData($data)
	{
		$contentFormatted = [];

		if (isset($data['content']) && is_array($data['content'])) {

			foreach ($data['content'] as $contentTypeItems) {

				$contentFormatted = array_merge($contentFormatted, $contentTypeItems);
			}

			foreach ($contentFormatted as $index => $contentItem) {

				if (isset($contentItem['removed']) && $contentItem['removed'] == 1) {

					unset($contentFormatted[$index]);
				}
			}

			$data['content'] = $contentFormatted;
		}

		return $data;
	}

}