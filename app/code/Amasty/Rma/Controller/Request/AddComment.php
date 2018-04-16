<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Controller\Request;

class AddComment extends \Amasty\Rma\Controller\Request
{
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');

        /** @var \Amasty\Rma\Model\Request $request */
        $request = $this->_initRequest($id);

        if (!$request) {
            return $this->goHome();
        }

        $content = $this->getRequest()->getParam('comment');

        if ($content) {

            $request->setData('comment', $content);

            try {
                /** @var \Amasty\Rma\Model\Comment $comment */
                $comment = $this->_objectManager->create('\Amasty\Rma\Model\Comment');
                $comment->submit($request, [
                    'value' => $content,
                    'is_admin' => false
                ]);

                $request->sendNotification2admin($comment);

                $request->save();

                $this->messageManager->addSuccessMessage(
                    __('Comment placed')
                );
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }

            return $this->_redirect(
                'amasty_rma/request/view', 
                ['id' => $id]
            );
        }
    }
}
