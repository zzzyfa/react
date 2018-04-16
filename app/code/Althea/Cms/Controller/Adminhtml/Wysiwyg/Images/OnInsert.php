<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 09/08/2017
 * Time: 12:28 PM
 */

namespace Althea\Cms\Controller\Adminhtml\Wysiwyg\Images;

use Magento\Cms\Controller\Adminhtml\Wysiwyg\Images;
use Magento\Store\Model\Store;

class OnInsert extends Images {

	/**
	 * @var \Magento\Framework\Controller\Result\RawFactory
	 */
	protected $resultRawFactory;

	/**
	 * @param \Magento\Backend\App\Action\Context             $context
	 * @param \Magento\Framework\Registry                     $coreRegistry
	 * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
	 */
	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\Registry $coreRegistry,
		\Magento\Framework\Controller\Result\RawFactory $resultRawFactory
	)
	{
		$this->resultRawFactory = $resultRawFactory;

		parent::__construct($context, $coreRegistry);
	}

	/**
	 * Fire when select image
	 *
	 * @return \Magento\Framework\Controller\ResultInterface
	 */
	public function execute()
	{
		$helper   = $this->_objectManager->get('Magento\Cms\Helper\Wysiwyg\Images');
		$storeId  = $this->getRequest()->getParam('store');
		$filename = $this->getRequest()->getParam('filename');
		$filename = $helper->idDecode($filename);
//		$asIs     = $this->getRequest()->getParam('as_is');
		$asIs     = false; // althea: prevent rendering image as html tag

		if (!$storeId) {

			$storeId = Store::DEFAULT_STORE_ID;
		}

		$this->_objectManager->get('Magento\Catalog\Helper\Data')->setStoreId($storeId);
		$helper->setStoreId($storeId);

		$image = $helper->getImageHtmlDeclaration($filename, $asIs);

		/** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
		$resultRaw = $this->resultRawFactory->create();

		return $resultRaw->setContents(sprintf('"%s"', $image));
	}

}