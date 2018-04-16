<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 06/12/2017
 * Time: 4:48 PM
 */

namespace Althea\Aftership\Block;

trait TrackTrait {

	/* @var \Magento\Framework\Registry $_coreRegistry */
	protected $_coreRegistry;
	/* @var \Mrmonsters\Aftership\Model\ResourceModel\Track\CollectionFactory $_trackCollectionFactory */
	protected $_trackCollectionFactory;
	/* @var \Althea\Aftership\Helper\Data $_aftershipHelper */
	protected $_aftershipHelper;
	/* @var \Althea\Aftership\Helper\Config $_configHelper */
	protected $_configHelper;
	/* @var \Magento\Sales\Model\Order\Shipment\Track $_mageTrack */
	protected $_mageTrack;
	protected $_tracking;
	protected $_title;

	public function getMageTracking()
	{
		/* @var \Magento\Sales\Model\Order $order */
		if (!$this->_mageTrack && $order = $this->_coreRegistry->registry('current_order')) {

			/* @var \Magento\Sales\Model\Order\Shipment\Track $magentoTrack */
			$magentoTrack = $order->getTracksCollection()->getFirstItem();
			/* @var \Mrmonsters\Aftership\Model\Track $asTrack */
			$asTrack = $this->_trackCollectionFactory->create()
			                                         ->addFieldToFilter('order_id', ['eq' => $order->getIncrementId()])
			                                         ->addFieldToFilter('tracking_number', ['eq' => $magentoTrack->getTrackNumber()])
			                                         ->getFirstItem();

			if ($magentoTrack->getId()
				&& $asTrack->getTrackId() // don't show aftership template if not imported
				&& !$this->_tracking
			) {

				$this->_mageTrack = $magentoTrack;
			}
		}

		return $this->_mageTrack;
	}

	public function getTracking()
	{
		if ($magentoTrack = $this->getMageTracking()) {

			$slug            = $this->_configHelper->getExtensionCourierSlug($magentoTrack->getCarrierCode());
			$this->_tracking = $this->_aftershipHelper->getAftershipTracking($slug, $magentoTrack->getTrackNumber());
		}

		return $this->_tracking;
	}

	public function getTrackingTagLabel()
	{
		$label = __('Pending');

		if ($this->_tracking) {

			switch ($this->_tracking['tag']) {

				case 'InfoReceived':
					$label = __('Info Received');
					break;
				case 'InTransit':
					$label = __('In Transit');
					break;
				case 'OutForDelivery':
					$label = __('Out for Delivery');
					break;
				case 'FailedAttempt':
					$label = __('Failed Attempt');
					break;
				case 'Delivered':
					$label = __('Delivered');
					break;
				case 'Exception':
					$label = __('Exception');
					break;
				case 'Expired':
					$label = __('Expired');
					break;
				default:
					$label = __('Pending');
			}
		}

		return $label;
	}

	public function formatDateTime($dateTimeStr, $format)
	{
		$dateTime = new \DateTime($dateTimeStr);

		return $dateTime->format($format);
	}

	public function getCourierTitle()
	{
		if (!$this->_title && $this->_mageTrack) {

			$this->_title = $this->_configHelper->getExtensionCourierTitle($this->_mageTrack->getCarrierCode());
		}

		return $this->_title;
	}

}