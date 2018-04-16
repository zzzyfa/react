<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Controller\Guest;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Amasty\Rma\Helper\Guest as GuestHelper;
use Magento\Framework\Controller\ResultInterface;

class LoginPost extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Amasty\Rma\Model\Session
     */
    protected $rmaSession;
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var GuestHelper
     */
    protected $guestHelper;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    /**
     * @var \Amasty\Rma\Helper\Data
     */
    protected $helper;

    /**
     * @param Context                     $context
     * @param \Amasty\Rma\Model\Session   $rmaSession
     * @param GuestHelper                 $guestHelper
     * @param \Magento\Framework\Registry $registry
     * @param \Amasty\Rma\Helper\Data     $helper
     * @param PageFactory                 $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Amasty\Rma\Model\Session $rmaSession,
        GuestHelper $guestHelper,
        \Magento\Framework\Registry $registry,
        \Amasty\Rma\Helper\Data $helper,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->rmaSession = $rmaSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->guestHelper = $guestHelper;
        $this->registry = $registry;
        $this->helper = $helper;
    }

    public function execute()
    {
        $method = $this->getRequest()->getMethod();

        if (!$this->guestHelper->isGuestEnabled() && $method == 'POST') {
            return $this->_redirect('/');
        }
        
        $result = $this->guestHelper->loadValidOrder($this->getRequest());
        if ($result instanceof ResultInterface) {
            return $result;
        }
        
        $order = $this->registry->registry('current_order');

        if (!$order) {
            return $this->_redirect('/');
        }

        $this->rmaSession->setOrder($order);

        $orderId = $this->getRequest()->getParam('order_id') ?: $order->getId();

        if ($this->helper->canCreateRma($order)) {
            return $this->_redirect(
                'amasty_rma/request/new',
                ['order_id' => $orderId]
            );
        }
        else {
            return $this->_redirect('amasty_rma/guest/history');
        }
    }
}
