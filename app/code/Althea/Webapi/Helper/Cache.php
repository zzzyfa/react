<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 28/09/2017
 * Time: 6:48 PM
 */

namespace Althea\Webapi\Helper;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Cache extends AbstractHelper {

	protected $_cache;

	/**
	 * @inheritDoc
	 */
	public function __construct(Context $context, CacheInterface $cache)
	{
		$this->_cache = $cache;

		parent::__construct($context);
	}

	/**
	 * @param $cacheKey
	 * @return bool|mixed
	 */
	public function getCacheResult($cacheKey)
	{
		if ($result = $this->_cache->load($cacheKey)) {

			return unserialize($result);
		}

		return false;
	}

	/**
	 * @param       $cacheKey
	 * @param array $data
	 * @param int   $lifeTime
	 */
	public function setCacheResult($cacheKey, $data, $lifeTime = 86400)
	{
		$this->_cache->save(serialize($data), $cacheKey, ['webapi'], $lifeTime);
	}

	/**
	 * @param $cacheKey
	 * @return bool
	 */
	public function deleteCacheResult($cacheKey)
	{
		return $this->_cache->remove($cacheKey);
	}

}