<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 06/12/2017
 * Time: 6:08 PM
 */

namespace Althea\Aftership\Api;

use Althea\Aftership\Api\Data\TrackDataInterface;

interface TrackRepositoryInterface {

	/**
	 * @param int    $customerId
	 * @param string $orderId
	 *
	 * @return \Althea\Aftership\Api\Data\TrackDataInterface
	 */
	public function getTracking($customerId, $orderId);

}