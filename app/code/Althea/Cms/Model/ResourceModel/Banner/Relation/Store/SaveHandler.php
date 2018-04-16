<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 09/08/2017
 * Time: 4:59 PM
 */

namespace Althea\Cms\Model\ResourceModel\Banner\Relation\Store;

use Althea\Cms\Api\Data\BannerInterface;
use Althea\Cms\Model\ResourceModel\Banner;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

class SaveHandler implements ExtensionInterface {

	/**
	 * @var MetadataPool
	 */
	protected $metadataPool;

	/**
	 * @var Banner
	 */
	protected $resourceBanner;

	/**
	 * @param MetadataPool $metadataPool
	 * @param Banner       $resourceBanner
	 */
	public function __construct(
		MetadataPool $metadataPool,
		Banner $resourceBanner
	)
	{
		$this->metadataPool   = $metadataPool;
		$this->resourceBanner = $resourceBanner;
	}

	/**
	 * @param object $entity
	 * @param array  $arguments
	 * @return object
	 * @throws \Exception
	 */
	public function execute($entity, $arguments = [])
	{
		$entityMetadata = $this->metadataPool->getMetadata(BannerInterface::class);
		$linkField      = $entityMetadata->getLinkField();
		$connection     = $entityMetadata->getEntityConnection();
		$oldStores      = $this->resourceBanner->lookupStoreIds((int)$entity->getId());
		$newStores      = (array)$entity->getStores();
		$table          = $this->resourceBanner->getTable('althea_cms_banner_store');
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