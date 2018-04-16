<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 18/01/2018
 * Time: 9:46 AM
 */

namespace Althea\Checkout\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class MobileCheckoutSuccessRedirectObserver implements ObserverInterface {

	protected $_checkoutSession;
	protected $_customLogger;
	protected $_url;
	protected $_responseFactory;
	protected $_configHelper;
	protected $_mobileCheckoutHelper;

	/**
	 * MobileCheckoutRedirectObserver constructor.
	 *
	 * @param \Magento\Checkout\Model\Session        $checkoutSession
	 * @param \Althea\Checkout\Logger\CheckoutLogger $checkoutLogger
	 * @param \Magento\Framework\UrlInterface        $url
	 * @param \Magento\Framework\App\ResponseFactory $responseFactory
	 * @param \Althea\Checkout\Helper\Config         $config
	 * @param \Althea\Checkout\Helper\Mobile         $mobile
	 */
	public function __construct(
		\Magento\Checkout\Model\Session $checkoutSession,
		\Althea\Checkout\Logger\CheckoutLogger $checkoutLogger,
		\Magento\Framework\UrlInterface $url,
		\Magento\Framework\App\ResponseFactory $responseFactory,
		\Althea\Checkout\Helper\Config $config,
		\Althea\Checkout\Helper\Mobile $mobile
	)
	{
		$this->_checkoutSession      = $checkoutSession;
		$this->_customLogger         = $checkoutLogger;
		$this->_url                  = $url;
		$this->_responseFactory      = $responseFactory;
		$this->_configHelper         = $config;
		$this->_mobileCheckoutHelper = $mobile;

	}

	/**
	 * @inheritDoc
	 */
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$lastOrderId = $observer->getEvent()->getData('order_ids');

		if (!empty($lastOrderId[0]) && $this->_mobileCheckoutHelper->isMobileCheckout()) {

			// althea: toggle logging from config
			if ($this->_configHelper->getIsLogEnabled()) {

				$this->_customLogger->debug(sprintf("[last_order_id: %s][checkout_code: %s] mobile checkout success redirect", json_encode($lastOrderId), $this->_mobileCheckoutHelper->getMobileCheckoutSession()));
			}

			$redirectUrl = $this->_url->getUrl('althea_checkout/mobile/success', [
				'order_id' => $lastOrderId[0],
			]);

			$this->_responseFactory->create()->setRedirect($redirectUrl)->sendResponse();
		}
	}

}