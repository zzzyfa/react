<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 08/02/2018
 * Time: 12:34 PM
 */

namespace Althea\Checkout\Model;

use Magento\Framework\Exception\CouldNotSaveException;

class PaymentInformationManagement extends \Magento\Checkout\Model\PaymentInformationManagement {

	protected $_configHelper;
	protected $_customLogger;

	/**
	 * @inheritDoc
	 */
	public function __construct(
		\Althea\Checkout\Helper\Config $config,
		\Althea\Checkout\Logger\CheckoutLogger $checkoutLogger,
		\Magento\Quote\Api\BillingAddressManagementInterface $billingAddressManagement,
		\Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement,
		\Magento\Quote\Api\CartManagementInterface $cartManagement,
		\Magento\Checkout\Model\PaymentDetailsFactory $paymentDetailsFactory,
		\Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalsRepository
	)
	{
		$this->_configHelper = $config;
		$this->_customLogger = $checkoutLogger;

		parent::__construct($billingAddressManagement, $paymentMethodManagement, $cartManagement, $paymentDetailsFactory, $cartTotalsRepository);
	}

	/**
	 * @inheritDoc
	 */
	public function savePaymentInformationAndPlaceOrder(
		$cartId,
		\Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
		\Magento\Quote\Api\Data\AddressInterface $billingAddress = null
	)
	{
		$this->savePaymentInformation($cartId, $paymentMethod, $billingAddress);
		try {
			$orderId = $this->cartManagement->placeOrder($cartId);
		} catch (\Exception $e) {

			$msg = "An error occurred on the server. Please try to place the order again.";

			// althea: toggle logging from config
			if ($this->_configHelper->getIsLogEnabled()) {

				$msg .= " exp: {$e->getMessage()}";

				$this->_customLogger->debug(sprintf("[cart_id: %s] %s", $cartId, $e->getMessage()));
			}

			throw new CouldNotSaveException(__($msg), $e);
		}

		return $orderId;
	}

}