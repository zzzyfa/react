<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 04/10/2017
 * Time: 4:51 PM
 */

namespace Althea\ApiCache\Model;

use Althea\ApiCache\Api\ConfigManagementInterface;
use Magento\Framework\App\Cache\StateInterface;
use Magento\Framework\App\RequestInterface;

abstract class AbstractConfigManagement implements ConfigManagementInterface {

	const BASE_PATH = '/rest';

	/**
	 * @var RequestInterface
	 */
	protected $request;

	/**
	 * @var array
	 */
	protected $paths;

	/**
	 * @var StateInterface
	 */
	protected $state;

	public function __construct(
		RequestInterface $request,
		StateInterface $state,
		array $paths = []
	)
	{
		$this->request = $request;
		$this->paths   = $paths;
		$this->state   = $state;
	}

	/**
	 * @inheritDoc
	 */
	public function canCacheRequest()
	{
		// Make sure it is a rest-API call (at this level we cannot rely on detected area)
		$uriPath = $this->request->getRequestUri();

		if (strpos($uriPath, static::BASE_PATH . '/') !== 0) {

			return false;
		}

		// Not GET calls should not be cached
		if (strtoupper($this->request->getMethod()) != 'GET') {

			return false;
		}

		foreach ($this->paths as $code => $path) {

			if (preg_match('|' . $path . '|i', $uriPath)) {

				return true;
			}
		}

		return false;
	}

	/**
	 * @inheritDoc
	 */
	abstract public function isCacheEnabled();

}