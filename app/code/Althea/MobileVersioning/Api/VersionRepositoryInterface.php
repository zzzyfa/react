<?php
/**
 * Created by PhpStorm.
 * User: manadirmahi
 * Date: 29/09/2017
 * Time: 6:22 PM
 */

namespace Althea\MobileVersioning\Api;

interface VersionRepositoryInterface {

	/**
	 * @param int    $storeId
	 * @param string $platform
	 * @return \Althea\MobileVersioning\Api\Data\VersionInterface
	 */
	public function getVersion($storeId, $platform);

	/**
	 * @param int $storeId
	 * @return \Althea\MobileVersioning\Api\Data\VersionInterface[]
	 */
	public function getVersions($storeId);

}