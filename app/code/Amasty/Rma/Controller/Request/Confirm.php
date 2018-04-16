<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Controller\Request;

class Confirm extends \Amasty\Rma\Controller\Request
{
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');

        $request = $this->_initRequest($id);

        if (!$request)
            return $this->goHome();

        if (!$request->allowPrintLabel()) {
            return $this->_redirect('*/*/view', ['id' => $id]);
        }

        $request
            ->setData('is_shipped', true)
            ->save()
        ;

        $this->messageManager->addSuccessMessage(__('Shipping confirmed'));

        return $this->_redirect('*/*/view', ['id' => $id]);
    }
}
