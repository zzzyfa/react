<?php

namespace TemplateMonster\ShopByBrand\Block\Brand;


class ListProduct extends \Magento\Catalog\Block\Product\ListProduct
{

    /**
     * @var
     */
    protected $collection;

    /**
     * ListProduct constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param \Magento\Catalog\Model\ProductFactory $collection
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Catalog\Model\ProductFactory $collection,
        array $data)
    {
        $this->collection = $collection;
        parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $data);
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|\Magento\Eav\Model\Entity\Collection\AbstractCollection|null
     */
    protected function _getProductCollection()
    {
        if ($this->_productCollection === null) {
            $layer = $this->getLayer();

            $currentBrand = $this->_coreRegistry->registry('current_brand');
            if ($currentBrand) {
                $brandId = $currentBrand->getId();
                $productCollection = $layer
                    ->getProductCollection()
                    ->addAttributeToSelect('brand_id', 'left')
                    ->addAttributeToFilter([
                        [
                            'attribute' => 'brand_id',
                            ['eq' => $brandId],
                        ],
                        [
                            'attribute' => 'entity_id',
                            'in' => $currentBrand->getAssignedProductIds()
                        ],
                    ]);
                $this->_productCollection = $productCollection;

                $this->prepareSortableFieldsByCategory($layer->getCurrentCategory());
            }
        }

        return $this->_productCollection;
    }
}