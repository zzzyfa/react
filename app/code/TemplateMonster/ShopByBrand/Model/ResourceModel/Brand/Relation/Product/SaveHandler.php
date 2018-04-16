<?php

namespace TemplateMonster\ShopByBrand\Model\ResourceModel\Brand\Relation\Product;

use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use TemplateMonster\ShopByBrand\Model\ResourceModel\Brand;
use TemplateMonster\ShopByBrand\Api\Data;

class SaveHandler implements ExtensionInterface
{

    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var Brand
     */
    protected $resourceBrand;

    /**
     * @param MetadataPool $metadataPool
     * @param Page $resourcePage
     */
    public function __construct(
        MetadataPool $metadataPool,
        Brand $resourceBrand
    ) {
        $this->metadataPool = $metadataPool;
        $this->resourceBrand = $resourceBrand;
    }

    /**
     * @param object $entity
     * @param array $arguments
     * @return object
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        $this->_assignProductToBrand($entity);
    }

    protected function _assignProductToBrand($entity)
    {
        $entityMetadata = $this->metadataPool->getMetadata(Data\BrandInterface::class);
        $connection = $entityMetadata->getEntityConnection();
        $brandProductTable = $this->resourceBrand->getTable('tm_brand_product');
        $brandId = $entity->getId();
        $storeId = 0;

        if(!$brandId) {
            return $this;
        }

        $condition = ['brand_id = ?' => (int)$brandId];
        $connection->delete($brandProductTable, $condition);


        //return $this;
        $insertData = [];
        $productIds = $entity->getBrandProducts();
        if ($productIds && is_string($productIds)) {
            $productIds = json_decode($productIds,true);
                foreach($productIds as $productId) {
                    $insertData[] = [
                        'brand_id' => $brandId,
                        'product_id' => $productId,
                        'store_id' => $storeId
                    ];
            }
        }
        if($insertData) {
            $connection->insertMultiple($brandProductTable, $insertData);
        }
    }

}