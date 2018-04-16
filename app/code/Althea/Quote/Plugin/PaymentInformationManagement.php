<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 09/11/2017
 * Time: 11:11 AM
 */

namespace Althea\Quote\Plugin;

use Adyen\Payment\Helper\Data;
use Adyen\Payment\Helper\PaymentMethods;
use Adyen\Payment\Model\Ui\AdyenHppConfigProvider;
use Adyen\Payment\Model\Ui\AdyenOneclickConfigProvider;
use Magento\Checkout\Api\Data\PaymentDetailsExtensionFactory;
use Magento\Checkout\Model\PaymentInformationManagement as PaymentInformationManagementModel;
use Magento\Framework\App\ObjectManager;
use Magento\Quote\Model\QuoteRepository;
use MOLPay\Seamless\Model\Source\Channel;
use MOLPay\Seamless\Helper\Data as MolpayHelper;
use MOLPay\Seamless\Model\Ui\ConfigProvider;

class PaymentInformationManagement {

	protected $_extensionAttributeFactory;
	protected $_molpaySeamlessChannel;
	protected $_molpaySeamlessHelper;
	protected $_adyenHelper;
	protected $_adyenPaymentMethodsHelper;
	protected $_quoteRepository;

	/**
	 * PaymentInformationManagementAfter constructor.
	 *
	 * @param \Magento\Checkout\Api\Data\PaymentDetailsExtensionFactory $extensionAttributeFactory
	 * @param \MOLPay\Seamless\Model\Source\Channel                     $molpayChannel
	 * @param \MOLPay\Seamless\Helper\Data                              $molpayHelper
	 * @param \Adyen\Payment\Helper\Data                                $adyenHelper
	 * @param \Adyen\Payment\Helper\PaymentMethods                      $adyenPaymentMethodsHelper
	 * @param \Magento\Quote\Model\QuoteRepository                      $quoteRepository
	 */
	public function __construct(
		PaymentDetailsExtensionFactory $extensionAttributeFactory,
		Channel $molpayChannel,
		MolpayHelper $molpayHelper,
		Data $adyenHelper,
		PaymentMethods $adyenPaymentMethodsHelper,
		QuoteRepository $quoteRepository
	)
	{
		$this->_extensionAttributeFactory = $extensionAttributeFactory;
		$this->_molpaySeamlessChannel     = $molpayChannel;
		$this->_molpaySeamlessHelper      = $molpayHelper;
		$this->_adyenHelper               = $adyenHelper;
		$this->_adyenPaymentMethodsHelper = $adyenPaymentMethodsHelper;
		$this->_quoteRepository           = $quoteRepository;
	}

	public function aroundGetPaymentInformation(PaymentInformationManagementModel $subject, \Closure $proceed, $cartId)
	{
		$result             = $proceed($cartId);
		$methodsWithOptions = [ConfigProvider::CODE, AdyenOneclickConfigProvider::CODE, AdyenHppConfigProvider::CODE];
		$methodOptions      = [];

		// simulate setPaymentMethods function in Magento/Checkout/view/frontend/web/js/model/payment-service.js
		$this->_filterPaymentMethods($result);

		foreach ($result->getPaymentMethods() as $method) {

			if (!in_array($method->getCode(), $methodsWithOptions)) {

				continue;
			}

			$paymentMethodOptionsObj            = ObjectManager::getInstance()->create(\Althea\Quote\Model\PaymentMethodOptions::class);
			$paymentMethodOptionsObj->{'code'}  = $method->getCode();
			$paymentMethodOptionsObj->{'title'} = $method->getTitle();

			switch ($method->getCode()) {

				case ConfigProvider::CODE:
					$paymentMethodOptionsObj->{'options'} = $this->_getMolpayActiveChannels();
					break;
				case AdyenOneclickConfigProvider::CODE:
					$quote                                = $this->_quoteRepository->getActive($cartId);
					$customerId                           = $quote->getCustomerId();
					$storeId                              = $quote->getStoreId();
					$grandTotal                           = $quote->getGrandTotal();
					$recurringType                        = \Adyen\Payment\Model\RecurringType::ONECLICK;
					$cards                                = $this->_adyenHelper->getOneClickPaymentMethods($customerId, $storeId, $grandTotal, $recurringType);
					$paymentMethodOptionsObj->{'options'} = $cards;
					break;
				case AdyenHppConfigProvider::CODE:
					$options = [];

					foreach ($this->_adyenPaymentMethodsHelper->getPaymentMethods($cartId) as $code => $data) {

						$options[] = [
							'value' => $data['brandCode'],
							'label' => $data['title'],
						];
					}

					$paymentMethodOptionsObj->{'options'} = $options;
					break;
				default:
					$paymentMethodOptionsObj->{'options'} = [];
			}

			$methodOptions[] = $paymentMethodOptionsObj;
		}

		$extAttributes = $result->getExtensionAttributes();

		if (!$extAttributes) {

			$extAttributes = $this->_extensionAttributeFactory->create();
		}

		$extAttributes->setPaymentMethodOptions($methodOptions);
		$result->setExtensionAttributes($extAttributes);

		return $result;
	}

	protected function _getMolpayActiveChannels()
	{
		$channels       = explode(",", $this->_molpaySeamlessHelper->getActiveChannels());
		$allChannel     = $this->_molpaySeamlessChannel->toArray();
		$activeChannels = [];

		foreach ($allChannel as $k => $v) {

			if (in_array($k, $channels)) {

				$activeChannels[] = [
					'value' => $k,
					'label' => $v,
				];
			}
		}

		return $activeChannels;
	}

	/**
	 * Filter payment methods before rendering
	 *
	 * @param \Magento\Checkout\Api\Data\PaymentDetailsInterface $paymentDetails
	 */
	protected function _filterPaymentMethods(\Magento\Checkout\Api\Data\PaymentDetailsInterface &$paymentDetails)
	{
		$paymentMethods  = $paymentDetails->getPaymentMethods();
		$totals          = $paymentDetails->getTotals();
		$isFreeAvailable = false;

		try {

			array_walk($paymentMethods, [$this, '_isFreeAvailable']);
		} catch (\Exception $e) {

			$isFreeAvailable = true;
		}

		if ($isFreeAvailable && $totals->getGrandTotal() <= 0) {

			$paymentMethods = array_filter($paymentMethods, [$this, '_unsetNonFreePaymentMethods']);
		}

		$paymentDetails->setPaymentMethods($paymentMethods);
	}

	/**
	 * @param \Magento\Payment\Model\MethodInterface $method
	 * @throws \Exception
	 */
	protected function _isFreeAvailable(\Magento\Payment\Model\MethodInterface $method)
	{
		if ($method->getCode() === \Magento\Payment\Model\Method\Free::PAYMENT_METHOD_FREE_CODE) {

			throw new \Exception;
		}
	}

	/**
	 * @param \Magento\Payment\Model\MethodInterface $method
	 * @return bool
	 */
	protected function _unsetNonFreePaymentMethods(\Magento\Payment\Model\MethodInterface $method)
	{
		return $method->getCode() === \Magento\Payment\Model\Method\Free::PAYMENT_METHOD_FREE_CODE;
	}

}