<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Controller\Request;

class CommentLookup extends \Amasty\Rma\Controller\Request
{
    public function execute()
    {
        $key = $this->getRequest()->getParam('key');

        /** @var \Amasty\Rma\Model\Comment $comment */
        $comment = $this->_objectManager->create(
            '\Amasty\Rma\Model\Comment'
        );

        $comment->load($key, 'unique_key');

        if ($comment->getId()) {
            try {
                $this->rmaSession->loginByComment($comment);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        } else {
            $this->messageManager->addErrorMessage(__("Wrong key"));
        }

        if ($this->rmaSession->isLoggedIn()) {
            $url = $this->_url->getUrl(
                'amasty_rma/request/view',
                [
                    'id'        => $comment->getData('request_id'),
                    '_fragment' => 'comment_' . $comment->getId()
                ]
            );

            return $this->_redirect($url);
        } else {
            return $this->_redirect('amasty_rma/guest/login');
        }
    }
}
