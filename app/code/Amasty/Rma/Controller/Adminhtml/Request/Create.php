<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Controller\Adminhtml\Request;

use Amasty\Rma\Controller\Adminhtml\Request as RequestAction;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Model\Order;
use Magento\Store\Model\StoreManagerInterface;

class Create extends RequestAction
{
    /**
     * @var \Amasty\Rma\Helper\Data
     */
    protected $helper;
    /**
     * @var \Magento\Framework\Url
     */
    protected $frontendUrlBuilder;
    /**
     * @var \Amasty\Rma\Helper\Guest
     */
    protected $guestHelper;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Create constructor.
     *
     * @param Context                  $context
     * @param \Magento\Framework\Url   $frontendUrlBuilder
     * @param \Amasty\Rma\Helper\Data  $helper
     * @param \Amasty\Rma\Helper\Guest $guestHelper
     * @param StoreManagerInterface    $storeManager
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Url $frontendUrlBuilder,
        \Amasty\Rma\Helper\Data $helper,
        \Amasty\Rma\Helper\Guest $guestHelper,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);

        $this->helper = $helper;
        $this->frontendUrlBuilder = $frontendUrlBuilder;
        $this->guestHelper = $guestHelper;
        $this->storeManager = $storeManager;
    }

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('order_id');

        /** @var Order $order */
        $order = $this->_objectManager->create('\Magento\Sales\Model\Order');
        $order->load($id);

        if ($order->getId() && $this->helper->canCreateRma($order)) {
            $storeBaseUrl = $this->storeManager->getStore($order->getStoreId())->getBaseUrl();
            $url = $storeBaseUrl . 'amasty_rma/guest/loginPost/order_id/' . $order->getId();

            $this->guestHelper->authorizeOrder($order);

            $this->_redirect($url);
        }
        else {
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();

            $this->messageManager->addErrorMessage(__(
                'Can\'t create RMA for this order'
            ));

            return $resultRedirect->setRefererOrBaseUrl();
        }
    }
}
