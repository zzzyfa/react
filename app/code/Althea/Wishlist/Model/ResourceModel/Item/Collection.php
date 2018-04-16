<?php
/**
 * Created by PhpStorm.
 * User: manadirmahi
 * Date: 06/09/2017
 * Time: 7:01 PM
 */

namespace Althea\Wishlist\Model\ResourceModel\Item;

use Magento\Catalog\Api\Data\ProductInterface;

class Collection extends \Magento\Wishlist\Model\ResourceModel\Item\Collection
{

	/**
	 * Whether product status attribute value table is joined in select
	 *
	 * @var boolean
	 */
	protected $_isProductStatusJoined = false;

    /**
     * @inheritDoc
     */
    protected function _assignProducts()
    {
        \Magento\Framework\Profiler::start(
            'WISHLIST:' . __METHOD__,
            ['group' => 'WISHLIST', 'method' => __METHOD__]
        );

        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
        $productCollection = $this->_productCollectionFactory->create();

        if ($this->_productVisible) {
            $productCollection->setVisibility($this->_productVisibility->getVisibleInSiteIds());
        }

        $attributesToSelect = [
            'name',
            'visibility',
            'small_image',
            'thumbnail',
            'links_purchased_separately',
            'links_title',
            'brand_althea' // althea: add brand_althea to product collection item
        ];

        $productCollection->addPriceData()
            ->addTaxPercents()
            ->addIdFilter($this->_productIds)
            ->addAttributeToSelect($attributesToSelect)
            ->addOptionsToResult()
            ->addUrlRewrite();

        if ($this->_productSalable) {
            $productCollection = $this->_adminhtmlSales->applySalableProductTypesFilter($productCollection);
        }

        $this->_eventManager->dispatch(
            'wishlist_item_collection_products_after_load',
            ['product_collection' => $productCollection]
        );

        $checkInStock = $this->_productInStock && !$this->stockConfiguration->isShowOutOfStock();

        foreach ($this as $item) {
            $product = $productCollection->getItemById($item->getProductId());
            if ($product) {
                if ($checkInStock && !$product->isInStock()) {
                    $this->removeItemByKey($item->getId());
                } else {
                    $product->setCustomOptions([]);
                    $item->setProduct($product);
                    $item->setProductName($product->getName());
                    $item->setName($product->getName());
                    $item->setPrice($product->getPrice());
                }
            } else {
                $item->isDeleted(true);
            }
        }

        \Magento\Framework\Profiler::stop('WISHLIST:' . __METHOD__);

        return $this;
    }

	/**
	 * Adds filter on product status
	 *
	 * @param int $status
	 * @return $this
	 */
	public function addProductStatusFilter($status)
	{
		$this->_joinProductStatusTable();
		$this->getSelect()->where('INSTR(status_tbl.value, ?)', $status);

		return $this;
	}

	/**
	 * Joins product status attribute value to use it in WHERE and ORDER clauses
	 *
	 * @return $this
	 */
	protected function _joinProductStatusTable()
	{
		if (!$this->_isProductStatusJoined) {

			$entityTypeId = $this->_catalogConfFactory->create()
			                                          ->getEntityTypeId();
			/** @var \Magento\Catalog\Model\Entity\Attribute $attribute */
			$attribute      = $this->_catalogAttrFactory->create()
			                                            ->loadByCode($entityTypeId, 'status');
			$storeId        = $this->_storeManager->getStore(\Magento\Store\Model\Store::ADMIN_CODE)->getId();
			$entityMetadata = $this->getMetadataPool()->getMetadata(ProductInterface::class);

			$this->getSelect()->join(
				['status_tbl' => $attribute->getBackendTable()],
				sprintf(
					"status_tbl.%s = main_table.product_id AND status_tbl.store_id IN (%s) AND status_tbl.attribute_id = %s",
					$entityMetadata->getLinkField(),
					implode(",", [$storeId, $this->_storeManager->getStore()->getId()]),
					$attribute->getId()
				),
				[]
			);

			$this->_isProductStatusJoined = true;
		}

		return $this;
	}

}
