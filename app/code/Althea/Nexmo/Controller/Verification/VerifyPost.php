<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 01/08/2017
 * Time: 4:06 PM
 */

namespace Althea\Nexmo\Controller\Verification;

use Althea\Nexmo\Helper\Verification;
use Althea\Nexmo\Model\VerificationFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\UrlInterface;

class VerifyPost extends Action {

	protected $checkoutSession;
	protected $verificationHelper;
	protected $url;
	protected $verificationFactory;

	public function __construct(
		Context $context,
		Session $customerSession,
		Verification $verificationHelper,
		VerificationFactory $verificationFactory
	)
	{
		$this->customerSession     = $customerSession;
		$this->verificationHelper  = $verificationHelper;
		$this->url                 = $context->getUrl();
		$this->verificationFactory = $verificationFactory;

		parent::__construct($context);
	}

	public function execute()
	{
		$resultRedirect = $this->resultRedirectFactory->create();

		if (!$this->getRequest()->isPost()) {

			$url = $this->url->getUrl('*/*/request', ['_secure' => true]);

			$resultRedirect->setUrl($this->_redirect->error($url));

			return $resultRedirect;
		}

		$customerId   = $this->customerSession->getCustomerId();
		$requestId    = $this->getRequest()->getParam('request_id');
		$code         = $this->getRequest()->getParam('code');
		$verification = $this->verificationFactory->create();

		$verification->loadUnverifiedByRequestId($requestId);

		if (!$verification->getId()) {

			$url = $this->url->getUrl('*/*/request', ['_secure' => true]);

			$this->messageManager->addErrorMessage(__("Invalid request ID."));
			$resultRedirect->setUrl($this->_redirect->error($url));

			return $resultRedirect;
		} else if (!$code) {

			$url = $this->url->getUrl('*/*/request', ['_secure' => true]);

			$this->messageManager->addErrorMessage(__("Invalid verification code."));
			$resultRedirect->setUrl($this->_redirect->error($url));

			return $resultRedirect;
		}

		try {

			$eventId = $this->verificationHelper->validate($customerId, $requestId, $code);
			$url     = $this->url->getUrl('checkout', ['_secure' => true]);

			$this->messageManager->addSuccessMessage(__("Verification is successful."));
			$resultRedirect->setUrl($url);

			return $resultRedirect;
		} catch (\Exception $e) {

			$msg = null;

			if ($e->getMessage() && strlen($e->getMessage()) > 0) {

				$msg = $e->getMessage();
			}

			$url = $this->url->getUrl('*/*/verify', [
				'_secure'    => true,
				'request_id' => $requestId,
			]);

			$this->messageManager->addErrorMessage(__($msg));
			$resultRedirect->setUrl($url);

			return $resultRedirect;
		}
	}

}