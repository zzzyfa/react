<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Block\Request;

use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\ScopeInterface;

class View extends \Magento\Framework\View\Element\Template
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    /**
     * @var \Amasty\Rma\Helper\Data
     */
    protected $helper;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * History constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param ObjectManagerInterface                           $objectManager
     * @param \Magento\Framework\Registry                      $registry
     * @param \Amasty\Rma\Helper\Data                          $helper
     * @param \Magento\Customer\Model\Session                  $customerSession
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,

        ObjectManagerInterface $objectManager,

        \Magento\Framework\Registry $registry,
        \Amasty\Rma\Helper\Data $helper,
        \Magento\Customer\Model\Session $customerSession,

        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->objectManager = $objectManager;
        $this->registry = $registry;
        $this->helper = $helper;
        $this->customerSession = $customerSession;

        $this->setTemplate('Amasty_Rma::guest/view.phtml');

        $request = $this->registry->registry('amrma_request');

        $this->setData('rma_request', $request);

        $this->setData(
            'rma_order',
            $this->registry->registry('amrma_order')
        );

        /** @var \Amasty\Rma\Model\ResourceModel\Item\Collection $collection */
        $collection = $this->objectManager->create('\Amasty\Rma\Model\ResourceModel\Item\Collection');
        $collection->addFilter('request_id', $request->getId());

        $this->setData('items', $collection);

        /** @var \Amasty\Rma\Model\ResourceModel\Comment\Collection $comments */
        $comments = $this->objectManager->create('\Amasty\Rma\Model\ResourceModel\Comment\Collection');
        $comments
            ->addFilter('request_id', $request->getId())
            ->setOrder('created_at', 'desc');
        $this->setData('comments', $comments);
    }

    public function getOrderUrl(\Amasty\Rma\Model\Request $request)
    {
        return $this->getUrl(
            'sales/order/view/',
            ['order_id' => $request->getData('order_id')]
        );
    }

    public function getAddress()
    {
        return $this->helper->getReturnAddress();
    }

    public function getShippingConfirmation()
    {
        return $this->_scopeConfig->getValue(
            'amrma/shipping/confirmation', ScopeInterface::SCOPE_STORE
        );
    }

    public function getIsAllowPrintLabel()
    {
        return $this->_scopeConfig->isSetFlag(
            'amrma/general/print_label', ScopeInterface::SCOPE_STORE
        );
    }

    public function getCustomerName()
    {
        if ($this->customerSession->isLoggedIn()) {
            return $this->customerSession->getCustomer()->getName();
        } else {
            /** @var \Magento\Sales\Model\Order $order */
            $order = $this->registry->registry('amrma_order');
            
            return $order->getBillingAddress()->getName();
        }
    }

    public function getFiles($commentId)
    {
        /** @var \Amasty\Rma\Model\ResourceModel\File\Collection $collection */
        $collection = $this->objectManager->create(
            '\Amasty\Rma\Model\ResourceModel\File\Collection'
        );
        
        $collection->addFilter('comment_id', $commentId);
        
        return $collection;
    }

    public function getSubmitUrl()
    {
        return $this->getUrl(
            '*/*/addComment',
            ['id' => (int)$this->getRequest()->getParam('id')]
        );
    }

    public function getExportUrl()
    {
        return $this->getUrl(
            '*/*/export', 
            ['id' => (int)$this->getRequest()->getParam('id')]
        );
    }

    public function getConfirmUrl()
    {
        return $this->getUrl(
            '*/*/confirm',
            ['id' => (int)$this->getRequest()->getParam('id')]
        );
    }

    public function getExtraTitle()
    {
        return $this->_scopeConfig->getValue(
            'amrma/extra/title', ScopeInterface::SCOPE_STORE
        );
    }
}
