<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 07/09/2017
 * Time: 3:15 PM
 */

namespace Althea\PaymentFilter\Controller\Adminhtml\Rule;

use Althea\PaymentFilter\Controller\Adminhtml\Rule;

class Index extends Rule {

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
	 * Index action
	 *
	 * @return \Magento\Framework\Controller\ResultInterface
	 */
	public function execute()
	{
		/** @var \Magento\Backend\Model\View\Result\Page $resultPage */
		$resultPage = $this->resultPageFactory->create();

		$this->initPage($resultPage)->getConfig()->getTitle()->prepend(__('Rules'));

		$dataPersistor = $this->_objectManager->get('Magento\Framework\App\Request\DataPersistorInterface');

		$dataPersistor->clear('paymentfilter_rules');

		return $resultPage;
	}

}