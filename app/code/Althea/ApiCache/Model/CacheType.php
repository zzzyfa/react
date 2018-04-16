<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 04/10/2017
 * Time: 6:29 PM
 */

namespace Althea\ApiCache\Model;

use Magento\Framework\App\Cache\Type\FrontendPool;
use Magento\Framework\Cache\Frontend\Decorator\TagScope;

class CacheType extends TagScope {

	const TYPE_IDENTIFIER = 'althea_apicache';
	const CACHE_TAG       = 'ALTHEA_API_CACHE_TAG';

	/**
	 * @param FrontendPool $cacheFrontendPool
	 */
	public function __construct(FrontendPool $cacheFrontendPool)
	{
		parent::__construct($cacheFrontendPool->get(self::TYPE_IDENTIFIER), self::CACHE_TAG);
	}

}