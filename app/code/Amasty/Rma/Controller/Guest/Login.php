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

class Login extends \Magento\Framework\App\Action\Action
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
     * @param Context                   $context
     * @param \Amasty\Rma\Model\Session $rmaSession
     * @param GuestHelper               $guestHelper
     * @param PageFactory               $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Amasty\Rma\Model\Session $rmaSession,
        GuestHelper $guestHelper,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->rmaSession = $rmaSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->guestHelper = $guestHelper;
    }

    public function execute()
    {
        if (!$this->guestHelper->isGuestEnabled()) {
            return $this->_redirect('/');
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('RMA Login'));

        return $resultPage;
    }
}
