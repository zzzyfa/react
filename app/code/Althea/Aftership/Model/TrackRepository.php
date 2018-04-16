<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 06/12/2017
 * Time: 6:34 PM
 */

namespace Althea\Aftership\Model;

use AfterShip\Trackings;
use Althea\Aftership\Api\Data\TrackDataInterface;
use Althea\Aftership\Api\TrackRepositoryInterface;
use Althea\Aftership\Helper\Config;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\OrderFactory;
use Mrmonsters\Aftership\Helper\Data;
use Mrmonsters\Aftership\Model\ResourceModel\Track\CollectionFactory;
use Mrmonsters\Aftership\Model\Track;

class TrackRepository implements TrackRepositoryInterface {

	protected $_configHelper;
	protected $_trackCollectionFactory;
	protected $_trackDataFactory;
	protected $_checkpointDataFactory;
	protected $_orderFactory;

	/**
	 * TrackRepository constructor.
	 *
	 * @param Config                $configHelper
	 * @param CollectionFactory     $trackCollectionFactory
	 * @param TrackDataFactory      $trackDataFactory
	 * @param CheckpointDataFactory $checkpointDataFactory
	 * @param OrderFactory          $orderFactory
	 */
	public function __construct(
		Config $configHelper,
		CollectionFactory $trackCollectionFactory,
		TrackDataFactory $trackDataFactory,
		CheckpointDataFactory $checkpointDataFactory,
		OrderFactory $orderFactory
	)
	{
		$this->_configHelper           = $configHelper;
		$this->_trackCollectionFactory = $trackCollectionFactory;
		$this->_trackDataFactory       = $trackDataFactory;
		$this->_checkpointDataFactory  = $checkpointDataFactory;
		$this->_orderFactory           = $orderFactory;
	}

	/**
	 * @inheritDoc
	 */
	public function getTracking($customerId, $orderId)
	{
		$enabled = $this->_configHelper->getExtensionEnabled();
		$key     = $this->_configHelper->getExtensionApiKey();
		$order   = $this->_orderFactory->create()->load($orderId);

		if (!$enabled) {

			throw new LocalizedException(__('Tracking extension is disabled.'));
		} else if (!$key) {

			throw new LocalizedException(__('Invalid extension key.'));
		} else if (!$order->getId()) {

			throw new NoSuchEntityException(__('Tracking not found.'));
		}

		/* @var Track $track */
		$track   = $this->_trackCollectionFactory->create()
		                                         ->addFieldToFilter('posted', array('eq' => Data::POSTED_DONE))
		                                         ->addFieldToFilter('order_id', array('eq' => $order->getIncrementId()))
		                                         ->getFirstItem();
		$trackNo = $track->getTrackingNumber();
		$slug    = $track->getShipCompCode();

		if (!$track->getId()
			|| !$trackNo
			|| !$slug
		) {

			// does not have enough info to get from aftership
			throw new NoSuchEntityException(__('Tracking not found.'));
		}

		try {

			$tracking = new Trackings($key);
			$response = $tracking->get($slug, $trackNo);
			/* @var TrackData $trackData */
			$trackData = $this->_trackDataFactory->create();

			foreach ($response['data']['tracking'] as $key => $val) {

				switch ($key) {

					case 'id':
						$trackData->setTrackingId($val);
						break;
					case 'tracking_number':
						$trackData->setTrackingNumber($val);
						break;
					case 'slug':
						$trackData->setSlug($val);
						break;
					case 'shipment_package_count':
						$trackData->setShipmentPackageCount($val);
						break;
					case 'shipment_pickup_date':
						$trackData->setShipmentPickupDate($val);
						break;
					case 'shipment_delivery_date':
						$trackData->setShipmentDeliveryDate($val);
						break;
					case 'tag':
						$trackData->setTag($val);
						break;
					case 'checkpoints':
						$checkpoints = [];

						foreach ($val as $cp) {

							/* @var CheckpointData $checkpoint */
							$checkpoint = $this->_checkpointDataFactory->create();

							$checkpoint->setSlug($cp['slug']);
							$checkpoint->setLocation($cp['location']);
							$checkpoint->setMessage($cp['message']);
							$checkpoint->setTag($cp['tag']);
							$checkpoint->setCheckpointTime($cp['checkpoint_time']);

							$checkpoints[] = $checkpoint;
						}

						$trackData->setCheckpoints($checkpoints);
						break;
				}
			}

			return $trackData;
		} catch (\AfterShip\AfterShipException $e) {

			if ($e->getCode() == '4004') { // not found

				throw new NoSuchEntityException(__('Tracking not found.'));
			}

			throw new LocalizedException(__($e->getMessage()));
		}
	}

}