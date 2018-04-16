<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 27/12/2017
 * Time: 5:37 PM
 */

namespace Althea\Freeshippinglabel\Controller\Adminhtml\Settings;

use Aheadworks\Freeshippinglabel\Api\LabelRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Aheadworks\Freeshippinglabel\Controller\Adminhtml\Settings\Index {

	protected $_resultPageFactory;
	protected $_coreRegistry;
	protected $_dataObjectProcessor;
	protected $_dataPersistor;
	protected $_labelRepository;

	/**
	 * @inheritDoc
	 */
	public function __construct(
		Context $context,
		LabelRepositoryInterface $labelRepository,
		PageFactory $resultPageFactory,
		Registry $coreRegistry,
		DataObjectProcessor $dataObjectProcessor,
		DataPersistorInterface $dataPersistor
	)
	{
		$this->_resultPageFactory   = $resultPageFactory;
		$this->_coreRegistry        = $coreRegistry;
		$this->_dataObjectProcessor = $dataObjectProcessor;
		$this->_dataPersistor       = $dataPersistor;
		$this->_labelRepository     = $labelRepository;

		parent::__construct($context, $labelRepository, $resultPageFactory, $coreRegistry, $dataObjectProcessor, $dataPersistor);
	}

	/**
	 * @inheritDoc
	 */
	public function execute()
	{
		/** @var \Magento\Backend\Model\View\Result\Page $resultPage */
		$resultPage = $this->_resultPageFactory->create();

		$this->_initPage($resultPage)
		     ->getConfig()
		     ->getTitle()
		     ->prepend(__('Labels'));

		$dataPersistor = $this->_objectManager->get('Magento\Framework\App\Request\DataPersistorInterface');

		$dataPersistor->clear('fslabel_label');

		return $resultPage;
	}

	/**
	 * @param Page $resultPage
	 *
	 * @return Page
	 */
	protected function _initPage(Page $resultPage)
	{
		$resultPage->setActiveMenu('Aheadworks_Freeshippinglabel::settings')
		           ->addBreadcrumb(__('Aheadworks'), __('Aheadworks'))
		           ->addBreadcrumb(__('Freeshipping Label'), __('Freeshipping Label'));

		return $resultPage;
	}

}