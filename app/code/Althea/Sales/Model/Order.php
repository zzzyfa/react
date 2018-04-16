<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 12/04/2018
 * Time: 6:12 PM
 */

namespace Althea\Sales\Model;

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Order extends \Magento\Sales\Model\Order {

	protected $_paymentMethods;
	protected $_notificationCollectionFactory;

	/**
	 * @inheritDoc
	 */
	public function __construct(
		\Adyen\Payment\Model\Resource\Notification\CollectionFactory $notificationCollectionFactory,
		\Magento\Framework\Model\Context $context,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
		AttributeValueFactory $customAttributeFactory,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Sales\Model\Order\Config $orderConfig,
		\Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
		\Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $orderItemCollectionFactory,
		\Magento\Catalog\Model\Product\Visibility $productVisibility,
		\Magento\Sales\Api\InvoiceManagementInterface $invoiceManagement,
		\Magento\Directory\Model\CurrencyFactory $currencyFactory,
		\Magento\Eav\Model\Config $eavConfig,
		\Magento\Sales\Model\Order\Status\HistoryFactory $orderHistoryFactory,
		\Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory $addressCollectionFactory,
		\Magento\Sales\Model\ResourceModel\Order\Payment\CollectionFactory $paymentCollectionFactory,
		\Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory $historyCollectionFactory,
		\Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory,
		\Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollectionFactory,
		\Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory $memoCollectionFactory,
		\Magento\Sales\Model\ResourceModel\Order\Shipment\Track\CollectionFactory $trackCollectionFactory,
		\Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollectionFactory,
		PriceCurrencyInterface $priceCurrency,
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productListFactory,
		\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
		\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
		array $data = []
	)
	{
		$this->_notificationCollectionFactory = $notificationCollectionFactory;

		parent::__construct($context, $registry, $extensionFactory, $customAttributeFactory, $timezone, $storeManager, $orderConfig, $productRepository, $orderItemCollectionFactory, $productVisibility, $invoiceManagement, $currencyFactory, $eavConfig, $orderHistoryFactory, $addressCollectionFactory, $paymentCollectionFactory, $historyCollectionFactory, $invoiceCollectionFactory, $shipmentCollectionFactory, $memoCollectionFactory, $trackCollectionFactory, $salesOrderCollectionFactory, $priceCurrency, $productListFactory, $resource, $resourceCollection, $data);
	}

	/**
	 * @inheritDoc
	 */
	protected function _canVoidOrder()
	{
		// althea:
		// - allow cancelling [adyen] order under payment_review state / status
		return !($this->isCanceled() || $this->canUnhold() || !$this->canCancelPaymentReview());
	}

	/**
	 * Check if [adyen] order is valid for cancellation (eg. failed authorization)
	 *
	 * @return bool
	 */
	public function canCancelPaymentReview()
	{
		$payment = $this->getPayment();

		if (!$this->isPaymentReview()
			|| !in_array($payment->getMethod(), $this->_getPaymentMethods())
		) {

			return false;
		}

		/* @var \Adyen\Payment\Model\Resource\Notification\Collection $successNotification */
		$successNotification = $this->_notificationCollectionFactory->create()
		                                                            ->addFieldToFilter('merchant_reference', ['eq' => $this->getIncrementId()])
		                                                            ->addFieldToFilter('pspreference', ['eq' => $payment->getAdditionalInformation('pspReference')])
		                                                            ->addFieldToFilter('done', ['eq' => 1])
		                                                            ->addFieldToFilter('success', ['eq' => 'true']);

		if ($successNotification->getSize() > 0) {

			return false;
		}

		return true;
	}

	protected function _getPaymentMethods()
	{
		if (!$this->_paymentMethods) {

			$this->_paymentMethods = [
				\Adyen\Payment\Model\Ui\AdyenCcConfigProvider::CODE,
				\Adyen\Payment\Model\Ui\AdyenOneclickConfigProvider::CODE,
			];
		}

		return $this->_paymentMethods;
	}

}