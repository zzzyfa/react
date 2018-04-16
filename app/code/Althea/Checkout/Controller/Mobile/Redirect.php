<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 23/10/2017
 * Time: 4:15 PM
 */

namespace Althea\Checkout\Controller\Mobile;

use Adyen\Payment\Model\Ui\AdyenCcConfigProvider;
use Adyen\Payment\Model\Ui\AdyenHppConfigProvider;
use Adyen\Payment\Model\Ui\AdyenOneclickConfigProvider;
use Althea\Checkout\Helper\Mobile;
use Althea\Customer\Model\AccountManagement;
use Magento\Checkout\Model\Session;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Paypal\Model\Config;
use MOLPay\Seamless\Model\Ui\ConfigProvider;

class Redirect extends Action {

	const ADYEN_CC_TYPE_VISA = 'visa';

	/* @var AccountManagement $_customerAccountManagement */
	protected $_customerAccountManagement;
	protected $_checkoutSession;
	protected $_mobileCheckoutHelper;

	/**
	 * @inheritDoc
	 */
	public function __construct(
		Context $context,
		AccountManagementInterface $customerAccountManagement,
		Session $checkoutSession,
		Mobile $mobile
	)
	{
		parent::__construct($context);

		$this->_customerAccountManagement = $customerAccountManagement;
		$this->_checkoutSession           = $checkoutSession;
		$this->_mobileCheckoutHelper      = $mobile;
	}

	/**
	 * @inheritDoc
	 */
	public function execute()
	{
		$customerToken = $this->getRequest()->getParam('token');
		$paymentMethod = $this->getRequest()->getParam('payment_method');

		try {

			/* @var CustomerInterface $customer */
			$customer = $this->_customerAccountManagement->authenticateUserToken($customerToken);

			$this->_checkoutSession->setCustomerData($customer);
			$this->_mobileCheckoutHelper->initMobileCheckoutSession();

			switch ($paymentMethod) {

				case AdyenCcConfigProvider::CODE:
					$encryptedData = $this->getRequest()->getParam('encrypted_data');
					$storeCc       = $this->getRequest()->getParam('store_cc');

					switch ($this->getRequest()->getParam('cc_type')) {

						case self::ADYEN_CC_TYPE_VISA:
							$ccType = 'vi';
							break;
						default:
							$ccType = $this->getRequest()->getParam('cc_type');
					}

					$this->_checkoutSession->setAltheaCheckoutEncryptedData($encryptedData);
					$this->_checkoutSession->setAltheaCheckoutCcType($ccType);
					$this->_checkoutSession->setAltheaCheckoutStoreCc($storeCc);

					$this->_redirect('*/*/adyenCc');
					break;
				case AdyenOneclickConfigProvider::CODE:
					$encryptedData = $this->getRequest()->getParam('encrypted_data');
					$referenceId   = $this->getRequest()->getParam('reference_id');
					$variant       = $this->getRequest()->getParam('variant');

					$this->_checkoutSession->setAltheaCheckoutEncryptedData($encryptedData);
					$this->_checkoutSession->setAltheaCheckoutReferenceId($referenceId);
					$this->_checkoutSession->setAltheaCheckoutVariant($variant);

					$this->_redirect('*/*/adyenOneClick');
					break;
				case AdyenHppConfigProvider::CODE:
					$brandCode = $this->getRequest()->getParam('brand_code');

					$this->_checkoutSession->setAltheaCheckoutBrandCode($brandCode);

					$this->_redirect('*/*/adyenHpp');
					break;
				case ConfigProvider::CODE:
					$paymentChannel = $this->getRequest()->getParam('payment_channel');

					$this->_checkoutSession->setAltheaCheckoutPaymentChannel($paymentChannel);

					$this->_redirect('*/*/molpaySeamless');
					break;
				case Config::METHOD_EXPRESS:
					$this->_redirect('*/*/paypalExpress');
					break;
				case \Magento\OfflinePayments\Model\Cashondelivery::PAYMENT_METHOD_CASHONDELIVERY_CODE:
					$this->_redirect('*/*/cashOnDelivery');
					break;
				case \Magento\Payment\Model\Method\Free::PAYMENT_METHOD_FREE_CODE:
					$this->_redirect('*/*/free');
					break;
				default:
					$redirectUrl = $this->_url->getUrl('althea_checkout/mobile/error', [
						'error_message' => 'payment method not available',
					]);
					$redirect    = $this->resultRedirectFactory->create()->setUrl($redirectUrl);

					return $redirect;
			}
		} catch (NoSuchEntityException $e) {

			$redirectUrl = $this->_url->getUrl('althea_checkout/mobile/error', [
				'error_message' => $e->getMessage(),
			]);
			$redirect    = $this->resultRedirectFactory->create()->setUrl($redirectUrl);

			return $redirect;
		} catch (AuthenticationException $e) {

			$redirectUrl = $this->_url->getUrl('althea_checkout/mobile/error', [
				'error_message' => $e->getMessage(),
			]);
			$redirect    = $this->resultRedirectFactory->create()->setUrl($redirectUrl);

			return $redirect;
		}
	}

}