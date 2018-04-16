<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Controller\Request;

use Magento\Framework\App\Action\Context;

class Edit extends \Amasty\Rma\Controller\Request
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * View constructor.
     *
     * @param Context                                    $context
     * @param \Magento\Customer\Model\Session            $customerSession
     * @param \Amasty\Rma\Model\Session                  $rmaSession
     * @param \Magento\Framework\Registry                $registry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Amasty\Rma\Model\Session $rmaSession,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context, $customerSession, $rmaSession, $registry);

        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        if (!$this->customerSession->isLoggedIn()) {
            $resultPage->addHandle('amasty_rma_guest');
        }

        $resultPage->getConfig()->getTitle()->prepend(__('Edit RMA'));

        $order = $this->registry->registry('current_order');

        $pageMainTitle = $resultPage->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            $pageMainTitle->setPageTitle(__(
                'New Return for Order #%1',
                $order->getData('increment_id')
            ));
        }

        $block = $resultPage->getLayout()->getBlock('customer.account.link.back');
        if ($block) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }

        return $resultPage;
    }
}
