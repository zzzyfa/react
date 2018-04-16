<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 05/12/2017
 * Time: 6:19 PM
 */

namespace Althea\Aftership\Helper;

use AfterShip\AfterShipException;
use AfterShip\LastCheckPoint;
use Magento\Directory\Model\CountryFactory;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\DB\TransactionFactory;
use Magento\Framework\Registry;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\OrderFactory;
use Mrmonsters\Aftership\Helper\Config;
use Mrmonsters\Aftership\Helper\Data as AftershipData;
use Mrmonsters\Aftership\Model\Track;
use Mrmonsters\Aftership\Model\TrackFactory;

class Data extends AftershipData {

	protected $_dbTransactionFactory;
	protected $_coreRegistry;
	protected $_altheaConfigHelper;
	protected $_trackCollectionFactory;

	/**
	 * @inheritDoc
	 */
	public function __construct(
		TransactionFactory $transactionFactory,
		Registry $registry,
		\Althea\Aftership\Helper\Config $altheaConfigHelper,
		\Mrmonsters\Aftership\Model\ResourceModel\Track\CollectionFactory $trackCollectionFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		TrackFactory $trackFactory,
		OrderFactory $orderFactory,
		CountryFactory $countryFactory,
		Config $configHelper,
		Context $context
	)
	{
		$this->_dbTransactionFactory   = $transactionFactory;
		$this->_coreRegistry           = $registry;
		$this->_altheaConfigHelper     = $altheaConfigHelper;
		$this->_trackCollectionFactory = $trackCollectionFactory;

		parent::__construct($storeManager, $trackFactory, $orderFactory, $countryFactory, $configHelper, $context);
	}

	public function createInvoice(Order $order)
	{
		if (!$order->canInvoice() || $order->hasInvoices()) {

			return false;
		}

		/* @var Invoice $invoice */
		$invoice = $order->prepareInvoice();

		$invoice->setRequestedCaptureCase(Invoice::CAPTURE_OFFLINE); // althea: COD payment is captured offline
		$invoice->register();

		$transaction = $this->_dbTransactionFactory->create()
		                                           ->addObject($invoice)
		                                           ->addObject($invoice->getOrder());

		$transaction->save();

		return true;
	}

	public function updateRegistryOrder(Order $order)
	{
		if ($this->_coreRegistry->registry('sales_order')) {

			$this->_coreRegistry->unregister('sales_order');
			$this->_coreRegistry->register('sales_order', $order);
		}

		if ($this->_coreRegistry->registry('current_order')) {

			$this->_coreRegistry->unregister('current_order');
			$this->_coreRegistry->register('current_order', $order);
		}
	}

	public function getTrackingUrl(Order $order)
	{
		$domain = $this->_altheaConfigHelper->getExtensionTrackingDomain();
		/* @var Track $track */
		$track = $this->_trackCollectionFactory->create()
		                                       ->addFieldToFilter('posted', ['eq' => AftershipData::POSTED_DONE])
		                                       ->addFieldToFilter('order_id', ['eq' => $order->getIncrementId()])
		                                       ->getFirstItem();

		if (!$this->_configHelper->getExtensionEnabled()
			|| !$domain
			|| !$track->getTrackingNumber()
		) {

			return false;
		}

		return sprintf("%s%s", $domain, $track->getTrackingNumber());
	}

	public function getAftershipTracking($slug, $trackNo)
	{
		$checkpoint = false;
		$client     = new LastCheckPoint($this->_configHelper->getExtensionApiKey());

		try {

			$response = $client->get($slug, $trackNo);

			if (!empty($response['data'])) {

				$checkpoint = $response['data'];
			}

			return $checkpoint;
		} catch (AfterShipException $e) {

			return false;
		}
	}

}