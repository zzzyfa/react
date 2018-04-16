<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Controller\Request;

class Delete extends \Amasty\Rma\Controller\Request
{
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');

        /** @var \Amasty\Rma\Model\Request $request */
        if ($request = $this->_initRequest($id)) {
            $request->delete();

            $this->messageManager->addSuccessMessage(
                __('Return request successfully deleted')
            );
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setRefererUrl();
    }
}
