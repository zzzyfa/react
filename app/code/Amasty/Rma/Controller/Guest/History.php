<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Controller\Guest;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class History extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var \Amasty\Rma\Helper\Data
     */
    protected $helper;
    /**
     * @var \Amasty\Rma\Model\Session
     */
    protected $rmaSession;

    /**
     * @param Context                   $context
     * @param PageFactory               $resultPageFactory
     * @param \Amasty\Rma\Helper\Data   $helper
     * @param \Amasty\Rma\Model\Session $rmaSession
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,

        \Amasty\Rma\Helper\Data $helper,
        \Amasty\Rma\Model\Session $rmaSession
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);

        $this->helper = $helper;
        $this->rmaSession = $rmaSession;
    }

    public function execute()
    {
        if (!$this->rmaSession->isLoggedIn())
            return $this->_redirect('*/*/login');
        
        $order = $this->rmaSession->getOrder();
        $requestsCount = $this->helper->getRequestsCount($order->getId());
        $canCreateRma = $this->helper->canCreateRma($order);

        if ($requestsCount == 0 && $canCreateRma) {
            return $this->_redirect(
                '*/request/new',
                ['order_id' => $order->getId()]
            );
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('My Return Requests'));

        $block = $resultPage->getLayout()->getBlock('customer.account.link.back');
        if ($block) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }

        return $resultPage;
    }
}
