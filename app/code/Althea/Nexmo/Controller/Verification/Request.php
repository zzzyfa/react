<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 26/07/2017
 * Time: 2:51 PM
 */

namespace Althea\Nexmo\Controller\Verification;

use Althea\Nexmo\Model\VerificationFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Request extends Action {

	protected $resultPageFactory;
	protected $customerSession;
	protected $verificationFactory;
	protected $url;

	public function __construct(
		Context $context,
		PageFactory $resultPageFactory,
		Session $customerSession,
		VerificationFactory $verificationFactory
	)
	{
		$this->resultPageFactory   = $resultPageFactory;
		$this->customerSession     = $customerSession;
		$this->verificationFactory = $verificationFactory;
		$this->url                 = $context->getUrl();

		parent::__construct($context);
	}

	public function execute()
	{
		$customerId   = $this->customerSession->getCustomerId();
		$verification = $this->verificationFactory->create();

		$verification->loadActiveByCustomer($customerId);

		if (!$this->customerSession->isLoggedIn() || $verification->getId()) {

			$resultRedirect = $this->resultRedirectFactory->create();
			$url            = $this->url->getUrl('checkout/index/index', ['_secure' => true]);

			$resultRedirect->setUrl($this->_redirect->success($url));

			return $resultRedirect;
		}

		$page = $this->resultPageFactory->create();

		$page->getConfig()
		     ->getTitle()
		     ->set(__('Checkout Verification Request'));

		return $page;
	}

}