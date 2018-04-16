<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 04/10/2017
 * Time: 4:50 PM
 */

namespace Althea\ApiCache\Api;

interface ConfigManagementInterface {

	/**
	 * Return true if can cache this request
	 *
	 * @return bool
	 */
	public function canCacheRequest();

	/**
	 * Return true if cache is enabled
	 *
	 * @return bool
	 */
	public function isCacheEnabled();

}