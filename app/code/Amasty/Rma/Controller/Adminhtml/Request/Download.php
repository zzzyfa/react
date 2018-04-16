<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Controller\Adminhtml\Request;

use Amasty\Rma\Controller\Adminhtml\Request as RequestAction;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;

class Download extends RequestAction
{
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @param Context                                          $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        parent::__construct($context);

        $this->fileFactory = $fileFactory;
    }

    public function execute()
    {
        $fileId = $this->getRequest()->getParam('id', 0);

        /** @var \Amasty\Rma\Model\File $file */
        $file = $this->_objectManager->create('\Amasty\Rma\Model\File');

        $file->load($fileId);

        if (!$file->getId()) {
            $this->messageManager->addErrorMessage(__('File not found'));

            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('*/*/index');
        }

        $response = $this->fileFactory->create(
            $file->getData('name'), 
            ['type' => 'filename', 'value' => $file->getRelativeFilePath()],
            DirectoryList::MEDIA
        );

        return $response;
    }
}
