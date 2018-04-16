<?php

/**
 *
 * Copyright © 2015 TemplateMonster. All rights reserved.
 * See COPYING.txt for license details.
 *
 */

namespace TemplateMonster\FeaturedProduct\Block\FeaturedProduct\Widget;

use Magento\Customer\Model\Context as CustomerContext;

class Product extends \Magento\Catalog\Block\Product\AbstractProduct implements \Magento\Widget\Block\BlockInterface
{

    /**
     * Default cache time
     */
    const CACHE_TIME = 86400;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Widget\Helper\Conditions
     */
    protected $conditionsHelper;

    /**
     * Category collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_categoryCollectionFactory;

    protected $_template = 'TemplateMonster_FeaturedProduct::widget/products.phtml';
    protected $_productFactory;
    protected $_imageBuilder;
    protected $_httpContext;

    protected $_isCategoriesTab;


    public function __construct(
        \Magento\Directory\Model\PriceCurrency $priceCurrency,
        \Magento\Widget\Helper\Conditions $conditionsHelper,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Catalog\Block\Product\Context $context,
        \TemplateMonster\FeaturedProduct\Model\ProductFactory $productFactory,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = [])
    {
        $this->priceCurrency = $priceCurrency;
        $this->conditionsHelper = $conditionsHelper;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_imageBuilder = $context->getImageBuilder();
        $this->_productFactory = $productFactory;
        $this->_httpContext = $httpContext;
        $this->_isCategoriesTab = false;
        parent::__construct($context, $data);
    }


    public function _construct()
    {
        $this->addData(
            ['cache_lifetime' => self::CACHE_TIME, 'cache_tags' => [\Magento\Catalog\Model\Product::CACHE_TAG]]
        );
        parent::_construct();
    }

    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $cacheKeyFromData = "TM_";
        $configData = $this->getData();

        foreach($configData as $value) {
            if(is_string($value)) {
                $cacheKeyFromData .= $value;

            }
        }
        return [
            $cacheKeyFromData,
            $this->_storeManager->getStore()->getId(),
            $this->_design->getDesignTheme()->getId(),
            $this->_httpContext->getValue(CustomerContext::CONTEXT_GROUP),
            'template' => $this->getTemplate()
        ];
    }

    public function getCacheLifetime()
    {
        if(!$this->getData('cache_lifetime')) {
            return self::CACHE_TIME;
        }
        return $this->getData('cache_lifetime');
    }

    public function getProductIds()
    {
        $productArr = [];
        //TODO: temporary hide manual products
//        $productIdsStr = $this->getData('product_ids');
        $productIdsStr = false;
        if($productIdsStr && is_string($productIdsStr)) {
            $productArr = explode(',',$productIdsStr);
        }
        return $productArr;
    }

    public function getItemWidth()
    {
        $productItemWidth = '';
        $productPerRow = $this->getNumberPerRow();
        if ($productPerRow) {
            if (!$this->getShowCarousel()) {
                $productItemWidth = 'style="width: ' . 100 / $productPerRow . '%;"';
            }
        }
        return $productItemWidth;
    }

    /**
     * Get array of available titles
     *
     * @return array
     */

    public function getTextData()
    {
        return json_decode($this->getJsonData(), true);
    }

    /**
     * Get array of product types
     *
     * @return array
     */

    public function getTypes()
    {
        return explode(",", $this->getProductTypes());
    }

    /**
     * Get array of category ids
     *
     * @return array
     */

    public function getCategoryIds()
    {
        $conditions = $this->conditionsHelper->decode($this->getConditionsEncoded());
        $ids = [];
        if ($conditions) {
            $ids = array_key_exists('1--1', $conditions) ? explode(', ', $conditions['1--1']['value']) : [];
        }
        return empty($ids[0]) ? [] : $ids;
    }

    /**
     * Truncate product name
     *
     * @param string $name
     * @return string
     */
    public function truncateProductName($name)
    {
        $name = $this->escapeHtml($name);
        $truncate = (int) $this->getProductNameLength();
        $nameLength = strlen($name);
        if($truncate != 0 && is_int($truncate) && $nameLength > $truncate){
            $name = substr($name, 0, $truncate) . '...';
        }
        return $name;
    }

    public function getCategoryNames()
    {

        $categories = $this->getCategoryIds();
        if(empty($categories)) {
            return [];
        }

        /** @var \Magento\Catalog\Model\ResourceModel\Category $collection */
        $collection = $this->_categoryCollectionFactory->create();
        $collection->addIdFilter($categories);

        $collection->addNameToResult();

        $names = [];
        foreach ($collection as $category) {
            /** @var \Magento\Catalog\Model\Category $category */
            $names[$category->getId()] = $category->getName();
        }

        return $names;
    }

    /**
     * Return Product Collection
     * @return array
     */
    protected function _getProductCollections()
    {
        $productIds = $this->getProductIds();
        $numberPerView = (int)$this->getNumberPerView();
        $productCollectionsArray = false;


        $tabsContent = $this->_getTabsType();
        $tabs = ($tabsContent['type'] == 'product_types') ? $tabsContent['primary'] : $tabsContent['secondary'];
        $categoryIds = ($tabsContent['type'] == 'product_types') ? $tabsContent['secondary'] : $tabsContent['primary'];

        foreach ($tabs as $products){
            try {
                $productFactory = $this->_productFactory->create($products);
            } catch(\Exception $e) {
                continue;
            }
            if (empty($categoryIds)){
                $productCollectionsArray[$products]['all'] = $productFactory->getPreparedCollection($numberPerView, $productIds);
            }
            foreach ($categoryIds as $categoryId){
                if($tabsContent['type'] == 'product_types') {
                    if(count($tabsContent['primary']) > 1) {
                        $productCollectionsArray[$products]['all'] = $productFactory->getPreparedCollection($numberPerView, $productIds, $categoryIds);
                        break;
                    } else {
                        $productCollectionsArray[$products][$categoryId] = $productFactory->getPreparedCollection($numberPerView, $productIds, $categoryId);
                    }
                } else {
                    $productCollectionsArray[$categoryId][$products] = $productFactory->getPreparedCollection($numberPerView, $productIds, $categoryId);
                }
            }
        }

        return $productCollectionsArray;
    }

    /**
     *
     */
    protected function _getTabsType()
    {
        $productTypesCount = count($this->getTypes());
        $categoryIdsCount = count($this->getCategoryIds());
        if($productTypesCount > 1 && $categoryIdsCount == 1) {
            $tabs = [
                'type'      => 'categories',
                'primary'   => $this->getCategoryIds(),
                'secondary' => $this->getTypes(),
            ];
        } else {
            $tabs = [
                'type'      => 'product_types',
                'primary'   => $this->getTypes(),
                'secondary' => $this->getCategoryIds(),
            ];
        }
        return $tabs;
    }


    public function isCategoryPrimaryTabs()
    {
        $tabs = $this->_getTabsType();
        return $tabs['type'] == 'categories';
    }

    public function isProductTypePrimaryTabs()
    {
        $tabs = $this->_getTabsType();
        return $tabs['type'] == 'product_types';
    }

    /**
     * Return HTML block with price
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $priceType
     * @param string $renderZone
     * @param array $arguments
     * @return string
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getProductPriceHtml(
        \Magento\Catalog\Model\Product $product,
        $priceType = null,
        $renderZone = \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
        array $arguments = []
    ) {
        if (!isset($arguments['zone'])) {
            $arguments['zone'] = $renderZone;
        }
        $arguments['zone'] = isset($arguments['zone'])
            ? $arguments['zone']
            : $renderZone;
        $arguments['price_id'] = isset($arguments['price_id'])
            ? $arguments['price_id']
            : 'old-price-' . $product->getId() . '-' . $priceType;
        $arguments['include_container'] = isset($arguments['include_container'])
            ? $arguments['include_container']
            : true;
        $arguments['display_minimal_price'] = isset($arguments['display_minimal_price'])
            ? $arguments['display_minimal_price']
            : true;

        /** @var \Magento\Framework\Pricing\Render $priceRender */
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product,
                $arguments
            );
        }
        return $price;
    }

    public function getProductsLabel($product, $label)
    {
        return $this->replaceString($product, $label);
    }

    public function replaceString($product, $string)
    {
        switch ($string) {

            case $this->findSubstring($string, "{SAVE_PERCENT}"):
                return $this->replaceSubstring("{SAVE_PERCENT}", $this->getSavePercent($product), $string);

            case $this->findSubstring($string, "{SAVE_AMOUNT}"):
                return $this->replaceSubstring("{SAVE_AMOUNT}", $this->getSaveAmount($product), $string);

            case $this->findSubstring($string, "{PRICE}"):
                return $this->replaceSubstring("{PRICE}", $this->getFormatedPrice($product), $string);

            case $this->findSubstring($string, "{SPECIAL_PRICE}"):
                return $this->replaceSubstring("{SPECIAL_PRICE}", $this->getSpecialPrice($product), $string);

            case $this->findSubstring($string, "{NEW_FOR}"):
                return $this->replaceSubstring("{NEW_FOR}", $this->getNewFor($product), $string);

            case $this->findSubstring($string, "{SKU}"):
                return $this->replaceSubstring("{SKU}", $this->getSku($product), $string);

            case $this->findSubstring($string, '{ATTR:'):
                return $this->attrCode($product, $string);

            case $this->findSubstring($string, "{BR}"):
                return $this->breakString($string);

            default:
                return $string;

        }
    }

    protected function findSubstring($string, $substring)
    {
        return (strpos($string, $substring) !== false);
    }

    protected function replaceSubstring($substring, $replacement, $string) {
        return str_replace($substring, $replacement, $string);
    }

    public function getSavePercent($product)
    {
        if ($product->getPrice() && $product->getFinalPrice()) {
            $savePercent =  ceil(
                100 - ((100 / $product->getPrice())
                    * $product->getFinalPrice())
            );
        } else {
            $savePercent = 0;
        }

        return $savePercent.'%';
    }

    public function getSaveAmount($product)
    {
        if ($product->getPrice() <= $product->getFinalPrice()) {
            return false;
        }
        $price = $product->getPrice() - $product->getFinalPrice();
        $priceInCurrentCurrency = $this->priceCurrency->convertAndRound($price);
        return $this->priceCurrency->format($priceInCurrentCurrency);
    }

    public function getFormatedPrice($product)
    {
        return $product->getFormatedPrice();
    }

    public function getSpecialPrice($product)
    {
        $specialPrice = $product->getSpecialPrice();
        return $this->priceCurrency->format($specialPrice);
    }

    public function getNewFor($product)
    {
        $created = $product->getCreatedAt();

        $dStart = new \DateTime();
        $dEnd  = new \DateTime($created);
        $dDiff = $dStart->diff($dEnd);

        if ($dDiff->days <= 0) {
            return false;
        }

        return $dDiff->days;
    }

    public function getSku($product)
    {
        return $product->getData('sku');
    }

    public function attrCode($product, $string)
    {
        preg_match("/{([^}]+)}*/i", $string, $placeholder);
        preg_match_all('/\{.*?}/', $placeholder[0], $placeholderText);
        $attr = str_replace(['{', '}', ':', 'ATTR'], '', $placeholderText[0][0]);
        $result = str_replace($placeholder[0], $product->getData($attr), $string);
        if (!$attr || !$result) {
            return false;
        }
        return $result;
    }

    public function breakString($string)
    {
        return str_replace('{BR}', '<br/>', $string);
    }

    /**
     * Prepare collection with new products
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _beforeToHtml()
    {
        $this->setProductCollections($this->_getProductCollections());
        return parent::_beforeToHtml();
    }

}