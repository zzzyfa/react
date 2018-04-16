<?php

namespace Althea\AutoCancelOrder\Observer;

use Magento\Framework\Event\ObserverInterface;

class PlaceOrderObserver implements ObserverInterface
{
    protected $_autoCancelOrderModel;
    protected $_configHelper;

	public function __construct(
		\Althea\AutoCancelOrder\Model\Cancel $autoCancelOrderModel,
		\Althea\AutoCancelOrder\Helper\Config $config
	)
	{
		$this->_autoCancelOrderModel = $autoCancelOrderModel;
		$this->_configHelper         = $config;
	}

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
    	if (!$this->_configHelper->getGeneralEnable()) {

    		return $this;
	    }

        $order = $observer->getEvent()->getOrder();
        $this->_autoCancelOrderModel->registerCancel($order);
        return $this;
    }
}
