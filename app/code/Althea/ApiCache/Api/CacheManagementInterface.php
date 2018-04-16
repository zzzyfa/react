<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 04/10/2017
 * Time: 4:50 PM
 */

namespace Althea\ApiCache\Api;

interface CacheManagementInterface {

	/**
	 * Get a cache response or false if not existing
	 *
	 * @return \Magento\Framework\App\ResponseInterface|false
	 */
	public function getCacheResult();

	/**
	 * Set a cache response for a request
	 *
	 * @param \Magento\Framework\App\ResponseInterface $response
	 * @return void
	 */
	public function setCacheResult(\Magento\Framework\App\ResponseInterface $response);

}