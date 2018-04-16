<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 09/08/2017
 * Time: 4:59 PM
 */

namespace Althea\Cms\Model\ResourceModel\Alert\Relation\Store;

use Althea\Cms\Api\Data\AlertInterface;
use Althea\Cms\Model\ResourceModel\Alert;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

class SaveHandler implements ExtensionInterface {

	/**
	 * @var MetadataPool
	 */
	protected $metadataPool;

	/**
	 * @var Alert
	 */
	protected $resourceAlert;

	/**
	 * @param MetadataPool $metadataPool
	 * @param Alert        $resourceAlert
	 */
	public function __construct(
		MetadataPool $metadataPool,
		Alert $resourceAlert
	)
	{
		$this->metadataPool  = $metadataPool;
		$this->resourceAlert = $resourceAlert;
	}

	/**
	 * @param object $entity
	 * @param array  $arguments
	 * @return object
	 * @throws \Exception
	 */
	public function execute($entity, $arguments = [])
	{
		$entityMetadata = $this->metadataPool->getMetadata(AlertInterface::class);
		$linkField      = $entityMetadata->getLinkField();
		$connection     = $entityMetadata->getEntityConnection();
		$oldStores      = $this->resourceAlert->lookupStoreIds((int)$entity->getId());
		$newStores      = (array)$entity->getStores();
		$table          = $this->resourceAlert->getTable('althea_cms_alert_store');
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