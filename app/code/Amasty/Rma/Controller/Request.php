<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Controller;

use Magento\Framework\App\Action\Context;
use Magento\Sales\Model\Order;

abstract class Request extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Amasty\Rma\Model\Session
     */
    protected $rmaSession;

    /**
     * @param Context                         $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Amasty\Rma\Model\Session       $rmaSession
     * @param \Magento\Framework\Registry     $registry
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Amasty\Rma\Model\Session $rmaSession,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);
        $this->registry = $registry;
        $this->customerSession = $customerSession;
        $this->rmaSession = $rmaSession;
    }

    /**
     * @param $id
     *
     * @return bool|\Amasty\Rma\Model\Request
     */
    protected function _initRequest($id)
    {
        $request = $this->_objectManager->create('\Amasty\Rma\Model\Request');
        $request->load($id);

        if ($this->_canViewRequest($request)) {

            $this->registry->register('amrma_request', $request, true);

            return $request;
        } else {
            $customerId = $this->customerSession->getCustomerId();

            if ($customerId) {
                $this->_redirect('amasty_rma/customer/history');
            } else {
                if ($this->rmaSession->getOrder()) {
                    $this->_redirect('amasty_rma/guest/history');
                } else {
                    $this->_redirect('amasty_rma/guest/login');
                }
            }
        }

        return false;
    }

    /**
     * @param \Amasty\Rma\Model\Request $request
     *
     * @return bool
     */
    protected function _canViewRequest(\Amasty\Rma\Model\Request $request)
    {
        if (!$request->getId()) {
            return false;
        }

        $customerId = $this->customerSession->getCustomerId();

        if ($customerId && ($request->getData('customer_id') == $customerId)) {
            return true;
        }

        $sessionOrder = $this->rmaSession->getOrder();

        if ($sessionOrder) { //guest validation
            /** @var Order $order */
            $order = $this->_objectManager->create('\Magento\Sales\Model\Order');
            $order->load($sessionOrder->getId());
            return $request->getData('email') == $order->getCustomerEmail();
        }

        return false;
    }

    /**
     * @param Order $order
     *
     * @return bool
     */
    protected function _canViewOrder(Order $order)
    {
        $customerId = $this->customerSession->getCustomerId();

        if ($order->getId() && $order->getCustomerId() && ($order->getCustomerId() == $customerId)) {
            return true;
        }

        $sessionOrder = $this->rmaSession->getOrder();

        if ($sessionOrder) {
            return $order->getCustomerEmail() == $sessionOrder->getCustomerEmail();
        }

        return false;
    }

    protected function goHome()
    {
        $controller = $this->rmaSession->getId() ? 'guest' : 'customer';

        return $this->_redirect("amasty_rma/$controller/history");
    }
}
