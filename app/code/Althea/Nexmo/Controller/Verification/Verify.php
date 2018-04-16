<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 01/08/2017
 * Time: 3:55 PM
 */

namespace Althea\Nexmo\Controller\Verification;

use Althea\Nexmo\Model\VerificationFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Result\PageFactory;

class Verify extends Action {

	protected $resultPageFactory;
	protected $verificationFactory;
	protected $url;

	public function __construct(
		Context $context,
		PageFactory $resultPageFactory,
		VerificationFactory $verificationFactory
	)
	{
		$this->resultPageFactory   = $resultPageFactory;
		$this->verificationFactory = $verificationFactory;
		$this->url                 = $context->getUrl();

		parent::__construct($context);
	}

	public function execute()
	{
		$requestId    = $this->getRequest()->getParam('request_id');
		$verification = $this->verificationFactory->create();

		$verification->loadUnverifiedByRequestId($requestId);

		if (!$verification->getId()) {

			$resultRedirect = $this->resultRedirectFactory->create();
			$url            = $this->url->getUrl('*/*/request', ['_secure' => true]);

			$this->messageManager->addErrorMessage(__("Invalid request ID."));
			$resultRedirect->setUrl($this->_redirect->error($url));

			return $resultRedirect;
		}

		$page = $this->resultPageFactory->create();

		$page->getConfig()
		     ->getTitle()
		     ->set(__('Checkout Verification Verify'));

		return $page;
	}

}