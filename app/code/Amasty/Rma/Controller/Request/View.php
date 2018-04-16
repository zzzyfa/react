<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Controller\Request;

use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;

class View extends \Amasty\Rma\Controller\Request
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * View constructor.
     *
     * @param Context                                    $context
     * @param \Magento\Customer\Model\Session            $customerSession
     * @param \Amasty\Rma\Model\Session                  $rmaSession
     * @param \Magento\Framework\Registry                $registry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param StoreManagerInterface                      $storeManager
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Amasty\Rma\Model\Session $rmaSession,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context, $customerSession, $rmaSession, $registry);

        $this->resultPageFactory = $resultPageFactory;
        $this->storeManager = $storeManager;
    }

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');

        $request = $this->_initRequest($id);
        if (!$request) {
            return $this->goHome();
        }

        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->_objectManager->create('\Magento\Sales\Model\Order');
        $order->load($request->getOrderId());

        $this->registry->register('amrma_order', $order, true);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        if (!$this->customerSession->isLoggedIn()) {
            $resultPage->addHandle('amasty_rma_guest');
        }

        $resultPage->getConfig()->getTitle()->prepend(__('My RMA'));

        $pageMainTitle = $resultPage->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            $pageMainTitle->setPageTitle(__('Return request for order #%1, status: %2',
                $request->getData('increment_id'),
                $pageMainTitle->escapeHtml($request->getStatusLabel())));
        }

        $block = $resultPage->getLayout()->getBlock('customer.account.link.back');
        if ($block) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }

        $breadcrumbs = $resultPage->getLayout()->getBlock('breadcrumbs');
        $homeUrl = 'amasty_rma/' . ($this->rmaSession->getId() ? 'guest' : 'customer') . '/history';
        $breadcrumbs
            ->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->storeManager->getStore()->getBaseUrl()
                ]
            )
            ->addCrumb(
                'amasty_rma_history',
                [
                    'label' => __('My RMA'),
                    'title' => __('Go to My RMA'),
                    'link' => $this->_url->getUrl($homeUrl)
                ]
            )
            ->addCrumb(
                'amasty_rma_view',
                [
                    'label' => __('RMA for order #%1', $request->getData('increment_id')),
                    'title' => __('RMA for order #%1', $request->getData('increment_id'))
                ]
            )
        ;

        return $resultPage;
    }
}
