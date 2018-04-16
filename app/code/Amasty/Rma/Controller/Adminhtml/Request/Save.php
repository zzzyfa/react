<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Controller\Adminhtml\Request;

use Amasty\Rma\Controller\Adminhtml\Request as RequestAction;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Backend\App\Action\Context;

class Save extends RequestAction
{
    /**
     * @var \Amasty\Rma\Helper\Data
     */
    protected $helper;

    /**
     * @param Context                 $context
     * @param \Amasty\Rma\Helper\Data $helper
     */
    public function __construct(
        Context $context,
        \Amasty\Rma\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->helper = $helper;
    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = (int)$this->getRequest()->getParam('id');

            /** @var \Amasty\Rma\Model\Request $model */
            $model = $this->_objectManager->create('Amasty\Rma\Model\Request')->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This request no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            try {
                if ($this->getRequest()->getParam('comment_submit')) {

                    $statusChanged = $model->getData('status_id') != $this->getRequest()->getParam('status_id');

                    $model->setData(
                        'status_id', $this->getRequest()->getParam('status_id')
                    );
                    $model->save();

                    /** @var \Amasty\Rma\Model\Comment $comment */
                    $comment = $this->_objectManager->create('\Amasty\Rma\Model\Comment');
                    $comment->submit($model, [
                        'value' => $this->getRequest()->getParam('comment'),
                        'is_admin' => true
                    ]);

                    $this->messageManager->addSuccessMessage(__('Comment saved'));

                    if ($this->getRequest()->getParam('notify_customer') && !$comment->getData('is_empty')) {
                        if ($model->sendNotification($comment, $statusChanged, true)) {
                            $this->messageManager->addSuccessMessage(__('Notification has been sent'));
                        }
                    }

                } else {
                    $model
                        ->addData($data)
                        ->setId($id)
                        ->save()
                        ->updateItemsQty()
                    ;

                    $this->messageManager->addSuccessMessage(__('Request has been successfully saved'));
                }

                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);

                if ($this->getRequest()->getParam('back') || $this->getRequest()->getParam('comment_submit')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData($data);

                return $resultRedirect->setPath('*/*/edit', [
                    'id' => $this->getRequest()->getParam('id')
                ]);
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
