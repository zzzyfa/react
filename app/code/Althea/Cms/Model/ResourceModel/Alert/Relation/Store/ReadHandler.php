<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 09/08/2017
 * Time: 4:58 PM
 */

namespace Althea\Cms\Model\ResourceModel\Alert\Relation\Store;

use Althea\Cms\Model\ResourceModel\Alert;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

class ReadHandler implements ExtensionInterface {

	/**
	 * @var Alert
	 */
	protected $resourceAlert;

	/**
	 * @param Alert $resourceAlert
	 */
	public function __construct(Alert $resourceAlert)
	{
		$this->resourceAlert = $resourceAlert;
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

			$stores = $this->resourceAlert->lookupStoreIds((int)$entity->getId());

			$entity->setData('store_id', $stores);
			$entity->setData('stores', $stores);
		}

		return $entity;
	}

}