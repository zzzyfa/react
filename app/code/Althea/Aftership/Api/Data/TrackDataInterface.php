<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 06/12/2017
 * Time: 6:10 PM
 */

namespace Althea\Aftership\Api\Data;

interface TrackDataInterface {

	const TRACKING_ID            = 'tracking_id';
	const TRACKING_NUMBER        = 'tracking_number';
	const SLUG                   = 'slug';
	const SHIPMENT_PACKAGE_COUNT = 'shipment_package_count';
	const SHIPMENT_PICKUP_DATE   = 'shipment_pickup_date';
	const SHIPMENT_DELIVERY_DATE = 'shipment_delivery_date';
	const TAG                    = 'tag';
	const CHECKPOINTS            = 'checkpoints';

	/**
	 * @return int|null
	 */
	public function getTrackingId();

	/**
	 * @return string|null
	 */
	public function getTrackingNumber();

	/**
	 * @return string|null
	 */
	public function getSlug();

	/**
	 * @return int|null
	 */
	public function getShipmentPackageCount();

	/**
	 * @return string|null
	 */
	public function getShipmentPickupDate();

	/**
	 * @return string|null
	 */
	public function getShipmentDeliveryDate();

	/**
	 * @return string|null
	 */
	public function getTag();

	/**
	 * @return \Althea\Aftership\Api\Data\CheckpointDataInterface[]|null
	 */
	public function getCheckpoints();

	/**
	 * @param int $tracking_id
	 *
	 * @return TrackDataInterface;
	 */
	public function setTrackingId($tracking_id);

	/**
	 * @param string $tracking_number
	 *
	 * @return TrackDataInterface
	 */
	public function setTrackingNumber($tracking_number);

	/**
	 * @param string $slug
	 *
	 * @return TrackDataInterface
	 */
	public function setSlug($slug);

	/**
	 * @param int $count
	 *
	 * @return TrackDataInterface
	 */
	public function setShipmentPackageCount($count);

	/**
	 * @param string $date
	 *
	 * @return TrackDataInterface
	 */
	public function setShipmentPickupDate($date);

	/**
	 * @param string $date
	 *
	 * @return TrackDataInterface
	 */
	public function setShipmentDeliveryDate($date);

	/**
	 * @param string $tag
	 *
	 * @return TrackDataInterface
	 */
	public function setTag($tag);

	/**
	 * @param \Althea\Aftership\Api\Data\CheckpointDataInterface[] $checkpoints
	 *
	 * @return TrackDataInterface
	 */
	public function setCheckpoints($checkpoints);

}