<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 04/10/2017
 * Time: 5:03 PM
 */

namespace Althea\ApiCache\Plugin;

use Althea\ApiCache\Api\CacheManagementInterface;
use Althea\ApiCache\Api\ConfigManagementInterface;

class AppInterfacePlugin {

	/**
	 * @var ConfigManagementInterface
	 */
	protected $configManagement;

	/**
	 * @var CacheManagementInterface
	 */
	protected $cacheManagement;

	public function __construct(ConfigManagementInterface $configManagement, CacheManagementInterface $cacheManagement)
	{
		$this->configManagement = $configManagement;
		$this->cacheManagement  = $cacheManagement;
	}

	public function aroundLaunch(\Magento\Framework\AppInterface $subject, \Closure $proceed)
	{
		if ($this->configManagement->isCacheEnabled() && $this->configManagement->canCacheRequest()) {

			$response = $this->cacheManagement->getCacheResult();

			if (!$response) {

				/** @var \Magento\Framework\App\ResponseInterface $response */
				$response = $proceed();

				$this->cacheManagement->setCacheResult($response);
			}
		} else {

			/** @var \Magento\Framework\App\ResponseInterface $response */
			$response = $proceed();
		}

		return $response;
	}

}