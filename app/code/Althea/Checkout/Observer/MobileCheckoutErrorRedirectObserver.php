<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 08/02/2018
 * Time: 1:45 PM
 */

namespace Althea\Checkout\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class MobileCheckoutErrorRedirectObserver implements ObserverInterface {

	protected $_checkoutSession;
	protected $_customLogger;
	protected $_url;
	protected $_responseFactory;
	protected $_configHelper;
	protected $_mobileCheckoutHelper;
	protected $_messageManager;

	/**
	 * MobileCheckoutRedirectObserver constructor.
	 *
	 * @param \Magento\Checkout\Model\Session             $checkoutSession
	 * @param \Althea\Checkout\Logger\CheckoutLogger      $checkoutLogger
	 * @param \Magento\Framework\UrlInterface             $url
	 * @param \Magento\Framework\App\ResponseFactory      $responseFactory
	 * @param \Althea\Checkout\Helper\Config              $config
	 * @param \Althea\Checkout\Helper\Mobile              $mobile
	 * @param \Magento\Framework\Message\ManagerInterface $messageManager
	 */
	public function __construct(
		\Magento\Checkout\Model\Session $checkoutSession,
		\Althea\Checkout\Logger\CheckoutLogger $checkoutLogger,
		\Magento\Framework\UrlInterface $url,
		\Magento\Framework\App\ResponseFactory $responseFactory,
		\Althea\Checkout\Helper\Config $config,
		\Althea\Checkout\Helper\Mobile $mobile,
		\Magento\Framework\Message\ManagerInterface $messageManager
	)
	{
		$this->_checkoutSession      = $checkoutSession;
		$this->_customLogger         = $checkoutLogger;
		$this->_url                  = $url;
		$this->_responseFactory      = $responseFactory;
		$this->_configHelper         = $config;
		$this->_mobileCheckoutHelper = $mobile;
		$this->_messageManager       = $messageManager;
	}

	/**
	 * @inheritDoc
	 */
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		if ($this->_mobileCheckoutHelper->isMobileCheckout()) {

			$lastErrMsg = $this->_messageManager->getMessages()
			                                    ->getLastAddedMessage();

			// althea: toggle logging from config
			if ($this->_configHelper->getIsLogEnabled()) {

				$this->_customLogger->debug(
					sprintf(
						"[cart_id: %s][checkout_code: %s] mobile checkout error redirect - %s",
						$this->_checkoutSession->getQuoteId(),
						$this->_mobileCheckoutHelper->getMobileCheckoutSession(),
						($lastErrMsg) ? $lastErrMsg->getText() : ''
					)
				);
			}

			$redirectUrl = $this->_url->getUrl('althea_checkout/mobile/error/', [
				'error_message' => 'Unable to complete payment',
			]);

			$this->_responseFactory->create()->setRedirect($redirectUrl)->sendResponse();
		}
	}

}