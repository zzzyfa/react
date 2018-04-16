<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 05/12/2017
 * Time: 4:05 PM
 */

namespace Althea\Aftership\Cron;

use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Iterator;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\ResourceModel\Order\Shipment\Track\CollectionFactory;
use Mrmonsters\Aftership\Helper\Config;
use Mrmonsters\Aftership\Helper\Data;
use Mrmonsters\Aftership\Model\ResourceModel\Track\CollectionFactory as AsTrackCollectionFactory;

class UploadTracking extends \Mrmonsters\Aftership\Cron\UploadTracking {

	protected $_resourceIterator;
	protected $_trackFactory;

	/**
	 * @inheritDoc
	 */
	public function __construct(
		Iterator $iterator,
		Order\Shipment\TrackFactory $trackFactory,
		ScopeConfigInterface $scopeConfig,
		CollectionFactory $collectionFactory,
		OrderFactory $orderFactory,
		Data $aftershipHelper,
		Config $configHelper,
		AsTrackCollectionFactory $asTrackCollectionFactory,
		ConfigInterface $configInterface,
		ManagerInterface $manager
	)
	{
		$this->_resourceIterator = $iterator;
		$this->_trackFactory     = $trackFactory;

		parent::__construct($scopeConfig, $collectionFactory, $orderFactory, $aftershipHelper, $configHelper, $asTrackCollectionFactory, $configInterface, $manager);
	}

	public function cron()
	{
		set_time_limit(0);

		$lastUpdate = $this->_configHelper->getExtensionLastUpdate();

		if (!$lastUpdate) { // althea: disable default config (upload shipments in past 3 hours during the first time)

			$lastUpdate = time();
		}

		$from = gmdate('Y-m-d H:i:s', $lastUpdate);
		$to   = gmdate('Y-m-d H:i:s');

		$trackCollection = $this->_trackCollectionFactory->create()
		                                                 ->addFieldToFilter('main_table.created_at', [
			                                                 'from' => $from,
			                                                 'to'   => $to,
		                                                 ]);

		$trackCollection->getSelect()
		                ->joinLeft(
			                ['as_track' => 'as_track'],
			                'main_table.track_number = as_track.tracking_number',
			                []
		                )
		                ->where('(as_track.tracking_number IS NULL) OR (as_track.posted = ?)', Data::POSTED_NOT_YET)
		                ->order('main_table.created_at DESC') // althea: prioritize latest shipment
		                ->group('main_table.parent_id');

		$this->_resourceIterator->walk(
			$trackCollection->getSelect(),
			[[$this, 'uploadTrackingCallback']]
		);

		$this->_eventManager->dispatch('aftership_track_cron_after', ['time' => time()]);
	}

	public function uploadTrackingCallback($args)
	{
		$row = $args['row'];
		/* @var Order\Shipment\Track $magentoTrack */
		$magentoTrack = $this->_trackFactory->create()->load($row['entity_id']);
		/* @var Order $order */
		$order       = $this->_orderFactory->create()->loadByIncrementId($row['order_id']);
		$enabled     = $this->_configHelper->getExtensionEnabled($order->getStore()->getWebsiteId());
		$cronEnabled = $this->_configHelper->getExtensionCronJobEnabled($order->getStore()->getWebsiteId());

		if ($enabled && $cronEnabled) {

			$tracks = $this->_asTrackCollectionFactory->create()
			                                          ->addFieldToFilter('tracking_number', array('eq' => $magentoTrack->getTrackNumber()))
			                                          ->getItems();
			$isSent = false;

			if (empty($tracks)) {

				// for the case that salesOrderShipmentTrackSaveAfter() is bypassed/crashed in shipment creation
				$track  = $this->_aftershipHelper->saveTrack($magentoTrack);
				$isSent = true;
			} else {

				$track = reset($tracks);

				if ($track->getPosted() == Data::POSTED_NOT_YET) {

					// for the case that the tracking was somehow failed to send to aftership
					$isSent = true;
				}

				// else its done or disabled, do nothing
			}

			if ($isSent) {

				$this->_aftershipHelper->sendTrack($track);
			}
		}
	}

}