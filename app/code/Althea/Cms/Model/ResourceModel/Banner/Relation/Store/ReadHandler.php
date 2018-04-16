<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 09/08/2017
 * Time: 4:58 PM
 */

namespace Althea\Cms\Model\ResourceModel\Banner\Relation\Store;

use Althea\Cms\Model\ResourceModel\Banner;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

class ReadHandler implements ExtensionInterface {

	/**
	 * @var Banner
	 */
	protected $resourceBanner;

	/**
	 * @param Banner $resourceBanner
	 */
	public function __construct(Banner $resourceBanner)
	{
		$this->resourceBanner = $resourceBanner;
	}

	/**
	 * @param object $entity
	 * @param array  $arguments
	 * @return object
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function execute($entity, $arguments = [])
	{
		if ($entity->getId()) {

			$stores = $this->resourceBanner->lookupStoreIds((int)$entity->getId());

			$entity->setData('store_id', $stores);
			$entity->setData('stores', $stores);
		}

		return $entity;
	}

}