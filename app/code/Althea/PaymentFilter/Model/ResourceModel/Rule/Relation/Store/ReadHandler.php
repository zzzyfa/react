<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 07/09/2017
 * Time: 4:05 PM
 */

namespace Althea\PaymentFilter\Model\ResourceModel\Rule\Relation\Store;

use Althea\PaymentFilter\Model\ResourceModel\Rule;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

class ReadHandler implements ExtensionInterface {

	/**
	 * @var Rule
	 */
	protected $resourceRule;

	/**
	 * @param Rule $resourceRule
	 */
	public function __construct(Rule $resourceRule)
	{
		$this->resourceRule = $resourceRule;
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

			$stores = $this->resourceRule->lookupStoreIds((int)$entity->getId());

			$entity->setData('store_id', $stores);
			$entity->setData('stores', $stores);
		}

		return $entity;
	}

}