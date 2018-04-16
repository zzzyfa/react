<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Controller\Request;

use Amasty\Rma\Controller\Adminhtml\Request as RequestAction;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Config\Definition\Exception\Exception;

class Download extends \Amasty\Rma\Controller\Request
{
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @param Context                                          $context
     * @param \Magento\Customer\Model\Session                  $customerSession
     * @param \Amasty\Rma\Model\Session                        $rmaSession
     * @param \Magento\Framework\Registry                      $registry
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Amasty\Rma\Model\Session $rmaSession,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        parent::__construct($context, $customerSession, $rmaSession, $registry);

        $this->fileFactory = $fileFactory;
    }

    public function execute()
    {
        $fileId = $this->getRequest()->getParam('id', 0);

        try {
            /** @var \Amasty\Rma\Model\File $file */
            $file = $this->_objectManager->create('\Amasty\Rma\Model\File');

            $file->load($fileId);

            if (!$file->getId()) {
                throw new LocalizedException(__('File not found'));
            }

            /** @var \Amasty\Rma\Model\Comment $file */
            $comment = $this->_objectManager->create('\Amasty\Rma\Model\Comment');
            $comment->load($file->getData('comment_id'));

            if (!$this->_initRequest($comment->getData('request_id')))
                throw new LocalizedException(__('File not found'));

            $response = $this->fileFactory->create(
                $file->getData('name'),
                ['type' => 'filename', 'value' => $file->getRelativeFilePath()],
                DirectoryList::MEDIA
            );
        }
        catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());

            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setRefererUrl();
        }

        return $response;
    }
}
