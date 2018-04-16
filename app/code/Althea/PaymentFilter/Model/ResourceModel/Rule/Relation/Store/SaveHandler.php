<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 07/09/2017
 * Time: 4:06 PM
 */

namespace Althea\PaymentFilter\Model\ResourceModel\Rule\Relation\Store;

use Althea\PaymentFilter\Api\Data\RuleInterface;
use Althea\PaymentFilter\Model\ResourceModel\Rule;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

class SaveHandler implements ExtensionInterface {

	/**
	 * @var MetadataPool
	 */
	protected $metadataPool;

	/**
	 * @var Rule
	 */
	protected $resourceRule;

	/**
	 * @param MetadataPool $metadataPool
	 * @param Rule         $resourceRule
	 */
	public function __construct(
		MetadataPool $metadataPool,
		Rule $resourceRule
	)
	{
		$this->metadataPool = $metadataPool;
		$this->resourceRule = $resourceRule;
	}

	/**
	 * @param object $entity
	 * @param array  $arguments
	 * @return object
	 * @throws \Exception
	 */
	public function execute($entity, $arguments = [])
	{
		$entityMetadata = $this->metadataPool->getMetadata(RuleInterface::class);
		$linkField      = $entityMetadata->getLinkField();
		$connection     = $entityMetadata->getEntityConnection();
		$oldStores      = $this->resourceRule->lookupStoreIds((int)$entity->getId());
		$newStores      = (array)$entity->getStores();
		$table          = $this->resourceRule->getTable('althea_paymentfilter_rule_store');
		$delete         = array_diff($oldStores, $newStores);

		if ($delete) {

			$where = [
				$linkField . ' = ?' => (int)$entity->getData($linkField),
				'store_id IN (?)'   => $delete,
			];

			$connection->delete($table, $where);
		}

		$insert = array_diff($newStores, $oldStores);

		if ($insert) {

			$data = [];

			foreach ($insert as $storeId) {

				$data[] = [
					$linkField => (int)$entity->getData($linkField),
					'store_id' => (int)$storeId,
				];
			}

			$connection->insertMultiple($table, $data);
		}

		return $entity;
	}

}