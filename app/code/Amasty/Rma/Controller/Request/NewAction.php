<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Controller\Request;

use Magento\Framework\App\Action\Context;

class NewAction extends \Amasty\Rma\Controller\Request
{
    /**
     * @var \Amasty\Rma\Helper\Data
     */
    protected $helper;

    /**
     * @param Context                         $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Amasty\Rma\Model\Session       $rmaSession
     * @param \Magento\Framework\Registry     $registry
     * @param \Amasty\Rma\Helper\Data         $helper
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Amasty\Rma\Model\Session $rmaSession,
        \Magento\Framework\Registry $registry,

        \Amasty\Rma\Helper\Data $helper
    ) {
        parent::__construct($context, $customerSession, $rmaSession, $registry);
        $this->helper = $helper;
    }

    public function execute()
    {
        $orderId = (int)$this->getRequest()->getParam('order_id');

        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->_objectManager->create('\Magento\Sales\Model\Order');

        $order->load($orderId);
        
        $this->registry->register('current_order', $order, true);

        if ($this->_canViewOrder($order) && $this->helper->canCreateRma($order, $message)) {
            $post = $this->getRequest()->getPost()->toArray();

            if ($post) {
                /** @var \Amasty\Rma\Model\Status $pending */
                $pending = $this->_objectManager
                    ->create('\Amasty\Rma\Model\Status')
                    ->load('pending', 'status_key')
                ;

                /** @var \Amasty\Rma\Model\Request $request */
                $request = $this->_objectManager->create('\Amasty\Rma\Model\Request');

                $request->setData([
                    'order_id'           => $order->getId(),
                    'increment_id'       => $order->getIncrementId(),
                    'store_id'           => $order->getStoreId(),
                    'customer_id'        => $order->getCustomerId(),
                    'email'              => $order->getCustomerEmail(),
                    'customer_firstname' => $order->getCustomerFirstname(),
                    'customer_lastname'  => $order->getCustomerLastname(),
                    'code'               => uniqid(),
                    'status_id'          => $pending->getId(),
                    'items'              => $this->getRequest()->getParam('items', []),
                    'comment'            => $this->getRequest()->getParam('comment', ''),
                    'field_1'            => $this->getRequest()->getParam('field_1'),
                    'field_2'            => $this->getRequest()->getParam('field_2'),
                    'field_3'            => $this->getRequest()->getParam('field_3'),
                    'field_4'            => $this->getRequest()->getParam('field_4'),
                    'field_5'            => $this->getRequest()->getParam('field_5')
                ]);

                $request->save();

                /** @var \Amasty\Rma\Model\Comment $comment */
                $comment = $this->_objectManager->create('\Amasty\Rma\Model\Comment');

                try {
                    $comment->submit(
                        $request,
                        [
                            'value'    => $this->getRequest()->getParam('comment'),
                            'is_admin' => false
                        ]
                    );
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                }
                
                $request->saveRmaItems([
                    'resolution' => $this->getRequest()->getParam('resolution'),
                    'condition'  => $this->getRequest()->getParam('condition'),
                    'reason'     => $this->getRequest()->getParam('reason'),
                ]);

                $request->sendNotificationRmaCreated($comment);

                $this->messageManager->addSuccessMessage(__(
                    'RMA has been successfully created'
                ));

                $this->_redirect(
                    'amasty_rma/request/view',
                    ['id' => $request->getId()]
                );
            } else {
                $this->_forward('edit');
            }
        } else {
            $this->messageManager->addErrorMessage(
                isset($message) ?
                __('Can\'t create RMA for this order: %1', $message)
                :
                __('Can\'t create RMA for this order')
            );
            
            return $this->goHome();
        }
    }
}
