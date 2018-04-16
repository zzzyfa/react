<?php
/**
 * Created by PhpStorm.
 * User: JUNGHO PARK
 * Date: 14/12/2017
 * Time: 11:24 AM
 */

namespace Althea\Catalog\Plugin\Model;

class ProductRepository
{
    protected $productCollectionFactory;
    protected $_productExtensionFactory;
    protected $_attributeFactory;

	public function __construct(
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
		\Magento\Catalog\Api\Data\ProductExtensionFactory $productExtensionFactory,
		\Althea\Catalog\Model\AttributeValueFactory $attributeValueFactory
	)
	{
		$this->productCollectionFactory = $productCollectionFactory;
		$this->_productExtensionFactory = $productExtensionFactory;
		$this->_attributeFactory        = $attributeValueFactory;
	}

    public function aroundGet(
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Closure $proceed,
        $sku,
        $editMode = false,
        $storeId = null,
        $forceReload = false
    )
    {
        /* @var \Magento\Catalog\Model\Product $result */
        $result = $proceed($sku, $editMode, $storeId, $forceReload);
        $linked_products = $result->getProductLinks();

        $linkedSkus = [];

        // Related product's sku to collect in separate array.
        foreach ($linked_products as $index => $value) {

            $linkedSkus[] = $value->getLinkedProductSku();
        }

        // Getting the products information using sku above.
        $collection = $this->productCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('sku', ['in' => $linkedSkus])
            ->addAttributeToFilter('status', ['eq' => \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED])
            ->addAttributeToFilter('visibility', ['neq' => \Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE])
            ->addMediaGalleryData();

        // Put datas in array.
        $visibleList = [];
        foreach ($collection as $item) {

            $visibleList[$item->getSku()] = $item;
        }

        // If each product's SKU is not in 'VISIBILITY' list, it will be unset in result
        // And other's there will be put 'extension attributes' in result.
        foreach ($linked_products as $index => $value) {

            if (!array_key_exists($value->getLinkedProductSku(), $visibleList)) {

                unset($linked_products[$index]);
                continue;
            }

            $extAttributes = $value->getExtensionAttributes();

            $extAttributes->setId($visibleList[$value->getLinkedProductSku()]->getId());
            $extAttributes->setName($visibleList[$value->getLinkedProductSku()]->getName());
            $extAttributes->setPrice($visibleList[$value->getLinkedProductSku()]->getPrice());
            $extAttributes->setSpecialPrice($visibleList[$value->getLinkedProductSku()]->getSpecialPrice());
            $extAttributes->setMediaGalleryEntries($visibleList[$value->getLinkedProductSku()]->getMediaGalleryEntries());

            $value->setExtensionAttributes($extAttributes);
        }

        $result->setProductLinks($linked_products);

        // althea:
	    // - add product attributes with attribute_code, attribute_value and frontend label
		$this->_addProductExtensionAttributes($result);

        return $result;
    }

    protected function _addProductExtensionAttributes(\Magento\Catalog\Api\Data\ProductInterface &$result)
    {
	    $extAttributes = $result->getExtensionAttributes();

	    if (!$extAttributes) {

		    $extAttributes = $this->_productExtensionFactory->create();
	    }

	    $attributes = array_map(function (\Magento\Framework\Api\AttributeInterface $productAttribute) use ($result) {

		    $label     = $result->getResource()
		                        ->getAttribute($productAttribute->getAttributeCode())
		                        ->getFrontendLabel();
		    $attribute = $this->_attributeFactory->create();

		    $attribute->setAttributeCode($productAttribute->getAttributeCode());
		    $attribute->setValue($productAttribute->getValue());
		    $attribute->setFrontendLabel($label);

		    return $attribute;
	    }, $result->getCustomAttributes());

	    $extAttributes->setProductAttributes(array_filter($attributes));
	    $result->setExtensionAttributes($extAttributes);
    }

}