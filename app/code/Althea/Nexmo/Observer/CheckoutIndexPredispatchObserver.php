<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 25/07/2017
 * Time: 5:47 PM
 */

namespace Althea\Nexmo\Observer;

use Althea\Nexmo\Helper\Config;
use Althea\Nexmo\Model\VerificationFactory;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;

class CheckoutIndexPredispatchObserver implements ObserverInterface {

	protected $configHelper;
	protected $checkoutSession;
	protected $customerSession;
	protected $verificationFactory;
	protected $responseFactory;
	protected $url;

	/**
	 * CheckoutIndexPredispatchObserver constructor.
	 *
	 * @param $configHelper
	 */
	public function __construct(
		Config $configHelper,
		CheckoutSession $checkoutSession,
		Session $customerSession,
		VerificationFactory $verificationFactory,
		ResponseFactory $responseFactory,
		UrlInterface $url
	)
	{
		$this->configHelper        = $configHelper;
		$this->checkoutSession     = $checkoutSession;
		$this->customerSession     = $customerSession;
		$this->verificationFactory = $verificationFactory;
		$this->responseFactory     = $responseFactory;
		$this->url                 = $url;
	}

	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		if (!$this->configHelper->getGeneralEnable()
			|| !$this->configHelper->getIsCheckoutVerificationEnabled()
			|| !$this->customerSession->isLoggedIn()
		) {

			return;
		}

		$customerId   = $this->customerSession->getCustomerId();
		$verification = $this->verificationFactory->create();

		$verification->loadActiveByCustomer($customerId);

		if ($verification->getId()) {

			return;
		}

		$this->checkoutSession->setAltheaNexmoIsVerified(false);

		$redirectUrl = $this->url->getUrl('precheckout/verification/request');

		$this->responseFactory->create()
		                      ->setRedirect($redirectUrl)
		                      ->sendResponse();
	}

}