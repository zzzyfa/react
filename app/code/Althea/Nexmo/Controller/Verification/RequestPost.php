<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 01/08/2017
 * Time: 4:06 PM
 */

namespace Althea\Nexmo\Controller\Verification;

use Althea\Nexmo\Helper\Verification;
use Althea\Nexmo\Logger\NexmoLogger;
use libphonenumber\PhoneNumberUtil;
use Magento\Customer\Model\Session;
use Magento\Directory\Model\CountryFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

class RequestPost extends Action {

	protected $customerSession;
	protected $verificationHelper;
	protected $url;
	protected $scopeConfig;
	protected $storeManager;
	protected $countryFactory;
	protected $logger;

	public function __construct(
		Context $context,
		Session $customerSession,
		Verification $verificationHelper,
		ScopeConfigInterface $scopeConfig,
		StoreManagerInterface $storeManager,
		CountryFactory $countryFactory,
		NexmoLogger $logger
	)
	{
		$this->customerSession    = $customerSession;
		$this->verificationHelper = $verificationHelper;
		$this->url                = $context->getUrl();
		$this->scopeConfig        = $scopeConfig;
		$this->storeManager       = $storeManager;
		$this->countryFactory     = $countryFactory;
		$this->logger             = $logger;

		parent::__construct($context);
	}

	public function execute()
	{
		$resultRedirect = $this->resultRedirectFactory->create();
		$formatter      = PhoneNumberUtil::getInstance();
		$intlPhoneNo    = null;

		if (!$this->getRequest()->isPost()) {

			$url = $this->url->getUrl('*/*/request', ['_secure' => true]);

			$resultRedirect->setUrl($this->_redirect->error($url));

			return $resultRedirect;
		}

		$customerId  = $this->customerSession->getCustomerId();
		$countryCode = $this->getRequest()->getParam('countryCode');
		$phoneNo     = $this->getRequest()->getParam('phoneNo');

		if (!$countryCode) {

			// get store country if not set
			$countryCode = $this->scopeConfig->getValue('general/country/default');
		}

		$exceptionData = [
			'customerId'  => $customerId,
			'phoneNo'     => $phoneNo,
			'storeId'     => $this->storeManager->getStore()->getId(),
			'countryCode' => $countryCode,
		];

		try {

			$instance    = $formatter->parse($phoneNo, $countryCode);
			$intlPhoneNo = $formatter->format($instance, \libphonenumber\PhoneNumberFormat::E164);

			if (!$formatter->isValidNumber($instance)) { // check if number is valid

				$url     = $this->url->getUrl('*/*/request', ['_secure' => true]);
				$country = $this->countryFactory->create();

				$country->loadByCode($countryCode);
				$this->messageManager->addErrorMessage(sprintf(__("Invalid %s phone number."), $country->getName()));
				$resultRedirect->setUrl($this->_redirect->error($url));

				return $resultRedirect;
			} else if ($this->verificationHelper->isCustomerVerified($customerId)) { // check customer verification status

				$url = $this->url->getUrl('*/*/request', ['_secure' => true]);

				$this->messageManager->addErrorMessage(__("Customer has already been verified."));
				$resultRedirect->setUrl($this->_redirect->error($url));

				return $resultRedirect;
			} else if ($this->verificationHelper->isPhoneNumberInUse($intlPhoneNo)) { // check if phone number is in use

				$url = $this->url->getUrl('*/*/request', ['_secure' => true]);

				$this->messageManager->addErrorMessage(__("Phone number is already in use."));
				$resultRedirect->setUrl($this->_redirect->error($url));

				return $resultRedirect;
			}
		} catch (\libphonenumber\NumberParseException $e) {

			$url                        = $this->url->getUrl('*/*/request', ['_secure' => true]);
			$exceptionData['exception'] = $e->getMessage();

			$this->logger->debug(json_encode($exceptionData));
			$this->messageManager->addErrorMessage(__($e->getMessage()));
			$resultRedirect->setUrl($this->_redirect->error($url));

			return $resultRedirect;
		}

		try {

			$requestId = $this->verificationHelper->request($customerId, $intlPhoneNo, $countryCode);
			$url       = $this->url->getUrl('*/*/verify', [
				'_secure'    => true,
				'request_id' => $requestId,
			]);

			$resultRedirect->setUrl($this->_redirect->success($url));

			return $resultRedirect;
		} catch (\Exception $e) {

			$msg = null;

			if ($e->getMessage() && strlen($e->getMessage()) > 0) {

				$msg = $e->getMessage();
			}

			$url                        = $this->url->getUrl('*/*/request', ['_secure' => true]);
			$exceptionData['exception'] = $msg;

			$this->logger->debug(json_encode($exceptionData));
			$this->messageManager->addErrorMessage(__($msg));

			$resultRedirect->setUrl($this->_redirect->error($url));

			return $resultRedirect;
		}
	}

}