<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 04/10/2017
 * Time: 4:59 PM
 */

namespace Althea\ApiCache\Model;

use Althea\ApiCache\Api\CacheManagementInterface;
use Magento\Framework\Cache\Frontend\Decorator\TagScope;
use Magento\Framework\Webapi\Rest\Response;
use MSP\APIEnhancer\Api\CacheKeyProcessorInterface;
use MSP\APIEnhancer\Api\TagInterface;
use Zend\Http\Headers;

class CacheManagement implements CacheManagementInterface {

	const CACHE_TTL = 86400;

	/**
	 * @var TagScope
	 */
	protected $cacheType;

	/**
	 * @var Response
	 */
	protected $response;

	/**
	 * @var array
	 */
	protected $keys;

	/**
	 * @var TagInterface
	 */
	protected $tag;

	public function __construct(
		TagScope $cacheType,
		Response $response,
		TagInterface $tag,
		array $keys = []
	)
	{
		$this->cacheType = $cacheType;
		$this->keys      = $keys;
		$this->tag       = $tag;
		$this->response  = $response;
	}

	/**
	 * Get cache key from request
	 *
	 * @return string
	 */
	protected function getCacheKey()
	{
		$keyInfo = [];

		foreach ($this->keys as $code => $keyProcessor) {

			/** @var $keyProcessor CacheKeyProcessorInterface */
			$keyInfo = array_merge($keyInfo, $keyProcessor->getKeys());
		}

		return md5(serialize($keyInfo));
	}

	/**
	 * Get a cache response or false if not existing
	 *
	 * @return \Magento\Framework\App\ResponseInterface|false
	 */
	public function getCacheResult()
	{
		$cacheKey = $this->getCacheKey();

		if (!$this->cacheType->test($cacheKey)) {

			return false;
		}

		$cacheData = unserialize($this->cacheType->load($cacheKey));

		$this->response->setHttpResponseCode($cacheData['code']);
		$this->response->setHeaders(Headers::fromString($cacheData['headers']));
		$this->response->setBody($cacheData['body']);

		return $this->response;
	}

	/**
	 * Set a cache response for a request
	 *
	 * @param \Magento\Framework\App\ResponseInterface $response
	 * @return void
	 */
	public function setCacheResult(\Magento\Framework\App\ResponseInterface $response)
	{
		$cacheKey = $this->getCacheKey();
		$tags     = $this->tag->getTags();

		if (!count($tags)) {

			return;
		}

		$responseBody    = $response->getBody();
		$responseCode    = $response->getStatusCode();
		$responseHeaders = $response->getHeaders()->toString();
		$cacheData       = [
			'code'    => $responseCode,
			'headers' => $responseHeaders,
			'body'    => $responseBody,
		];

		$this->cacheType->save(serialize($cacheData), $cacheKey, $tags, static::CACHE_TTL);
	}

}