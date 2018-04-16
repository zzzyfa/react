<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 06/12/2017
 * Time: 6:49 PM
 */

namespace Althea\Aftership\Model;

use Althea\Aftership\Api\Data\CheckpointDataInterface;
use Althea\Aftership\Api\Data\TrackDataInterface;
use Magento\Framework\Model\AbstractModel;

class TrackData extends AbstractModel implements TrackDataInterface {

	/**
	 * @inheritDoc
	 */
	public function getTrackingId()
	{
		return $this->getData(self::TRACKING_ID);
	}

	/**
	 * @inheritDoc
	 */
	public function getTrackingNumber()
	{
		return $this->getData(self::TRACKING_NUMBER);
	}

	/**
	 * @inheritDoc
	 */
	public function getSlug()
	{
		return $this->getData(self::SLUG);
	}

	/**
	 * @inheritDoc
	 */
	public function getShipmentPackageCount()
	{
		return $this->getData(self::SHIPMENT_PACKAGE_COUNT);
	}

	/**
	 * @inheritDoc
	 */
	public function getShipmentPickupDate()
	{
		return $this->getData(self::SHIPMENT_PICKUP_DATE);
	}

	/**
	 * @inheritDoc
	 */
	public function getShipmentDeliveryDate()
	{
		return $this->getData(self::SHIPMENT_DELIVERY_DATE);
	}

	/**
	 * @inheritDoc
	 */
	public function getTag()
	{
		return $this->getData(self::TAG);
	}

	/**
	 * @inheritDoc
	 */
	public function getCheckpoints()
	{
		return $this->getData(self::CHECKPOINTS);
	}

	/**
	 * @inheritDoc
	 */
	public function setTrackingId($tracking_id)
	{
		return $this->setData(self::TRACKING_ID, $tracking_id);
	}

	/**
	 * @inheritDoc
	 */
	public function setTrackingNumber($tracking_number)
	{
		return $this->setData(self::TRACKING_NUMBER, $tracking_number);
	}

	/**
	 * @inheritDoc
	 */
	public function setSlug($slug)
	{
		return $this->setData(self::SLUG, $slug);
	}

	/**
	 * @inheritDoc
	 */
	public function setShipmentPackageCount($count)
	{
		return $this->setData(self::SHIPMENT_PACKAGE_COUNT, $count);
	}

	/**
	 * @inheritDoc
	 */
	public function setShipmentPickupDate($date)
	{
		return $this->setData(self::SHIPMENT_PICKUP_DATE, $date);
	}

	/**
	 * @inheritDoc
	 */
	public function setShipmentDeliveryDate($date)
	{
		return $this->setData(self::SHIPMENT_DELIVERY_DATE, $date);
	}

	/**
	 * @inheritDoc
	 */
	public function setTag($tag)
	{
		return $this->setData(self::TAG, $tag);
	}

	/**
	 * @inheritDoc
	 */
	public function setCheckpoints($checkpoints)
	{
		return $this->setData(self::CHECKPOINTS, $checkpoints);
	}

}