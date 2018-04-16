<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 27/11/2017
 * Time: 5:04 PM
 */

namespace Althea\ShopByBrand\Model;

use Althea\ShopByBrand\Api\BrandFollowerRepositoryInterface;
use Althea\ShopByBrand\Model\ResourceModel\Follower\CollectionFactory as BrandFollowerCollectionFactory;
use Magento\Framework\Data\Collection;
use Magento\Framework\Exception\NoSuchEntityException;
use TemplateMonster\ShopByBrand\Model\Brand;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use TemplateMonster\ShopByBrand\Model\ResourceModel\Brand\CollectionFactory as BrandCollectionFactory;

class BrandFollowerRepository implements BrandFollowerRepositoryInterface {

	CONST NOT_FOUND = -1;

	/**
	 * @var BrandCollectionFactory
	 */
	protected $brandCollectionFactory;

	/**
	 * @var BrandFollowerCollectionFactory
	 */
	protected $brandFollowerCollectionFactory;

	protected $storeManager;
	protected $searchResultsFactory;

	protected $productCollectionFactory;

    protected $extensionAttributesJoinProcessor;
    protected $metadataServiceInterface;

    protected $searchCriteriaBuilder;
    protected $productSearchResultsFactory;

    protected $productVisibility;
    protected $productStatus;

	/**
	 * Initialize service
	 *
	 * @param BrandFollowerCollectionFactory                $brandFollowerCollectionFactory
	 * @param BrandCollectionFactory                        $brandCollectionFactory
	 * @param \Magento\Framework\Api\SearchResultsInterface $searchResults
	 * @param \Magento\Store\Model\StoreManagerInterface    $storeManager
	 */
	public function __construct(
		BrandFollowerCollectionFactory $brandFollowerCollectionFactory,
		BrandCollectionFactory $brandCollectionFactory,
		\Magento\Framework\Api\SearchResultsInterface $searchResults,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Catalog\Api\Data\ProductSearchResultsInterfaceFactory $productSearchResultsFactory,
        \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $metadataServiceInterface,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility
    )
    {
        $this->brandFollowerCollectionFactory = $brandFollowerCollectionFactory;
        $this->brandCollectionFactory = $brandCollectionFactory;
        $this->storeManager = $storeManager;
        $this->searchResultsFactory = $searchResults;

        $this->productCollectionFactory = $productCollectionFactory;
        $this->metadataServiceInterface = $metadataServiceInterface;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->productSearchResultsFactory = $productSearchResultsFactory;

        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
    }

	/**
	 * @return mixed
	 *
	 * @throws NoSuchEntityException
	 */
	public function getByCurrentStoreId()
	{
		$searchResults = $this->searchResultsFactory;
		$websiteId     = $this->storeManager->getStore()->getWebsiteId();
		$collection    = $this->brandCollectionFactory->create()
		                                              ->addWebsiteFilter($websiteId)
		                                              ->addFieldToFilter('status', ['eq' => Brand::STATUS_ENABLED]);
		$items         = [];
		$item_count    = 0;

		/* @var Brand $item */
		foreach ($collection as $item) {

			array_push($items, $this->_toBrandArray($item, [
				'brand_id',
				'name',
				'url_key',
				'status',
				'title',
				'logo',
				'brand_banner',
				'product_banner',
				'short_description',
				'main_description',
				'meta_keywords',
				'meta_description',
				'website_ids',
			]));

			$item_count++;
		}

		$searchResults->setItems($items);
		$searchResults->setTotalCount($item_count);

		return $searchResults;
	}

	public function getSingleBrand($brand_id)
	{
		$searchResults = $this->searchResultsFactory;
		$collection    = $this->brandCollectionFactory->create()
		                                              ->addFieldToFilter('brand_id', ['eq' => $brand_id]);
		$items         = [];
		$item_count    = 0;

		/* @var Brand $item */
		foreach ($collection as $item) {

			array_push($items, $this->_toBrandArray($item, [
				'brand_id',
				'name',
				'url_key',
				'status',
				'title',
				'logo',
				'brand_banner',
				'product_banner',
				'short_description',
				'main_description',
				'meta_keywords',
				'meta_description',
				'website_ids',
			]));

			$item_count++;
		}

		$searchResults->setItems($items);
		$searchResults->setTotalCount($item_count);

		return $searchResults;
	}

	public function getMyList($customerId)
	{
		$searchResults = $this->searchResultsFactory;
		$collection    = $this->brandCollectionFactory->create();
		$items         = [];
		$item_count    = 0;

		$collection->getSelect()
		           ->joinLeft(
			           ['follower' => 'tm_brand_followers'],
			           'main_table.brand_id = follower.brand_id',
			           ['customer_id', 'is_active']
		           );
		$collection->addFieldToFilter('follower.customer_id', ['eq' => $customerId])
		           ->addFieldToFilter('follower.is_active', ['eq' => 1])
		           ->setOrder('main_table.brand_id', Collection::SORT_ORDER_ASC);

		/* @var Brand $item */
		foreach ($collection as $item) {

			array_push($items, $this->_toBrandArray($item, [
				'brand_id',
				'name',
				'url_key',
				'status',
				'title',
				'logo',
				'brand_banner',
				'product_banner',
				'short_description',
				'main_description',
				'meta_keywords',
				'meta_description',
				'website_ids',
			]));

			$item_count++;
		}

		$searchResults->setItems($items);
		$searchResults->setTotalCount($item_count);

		return $searchResults;
	}

	public function addFollowByToken($brandId, $customerId)
	{
		if ($this->isValidByBrandId($brandId) == self::NOT_FOUND) {

			return "NOT_FOUND CUSTOMER";
		}

		/* @var Follower $brandFollower */
		$brandFollower = $this->brandFollowerCollectionFactory->create()
		                                                      ->addFieldToFilter('customer_id', ['eq' => $customerId])
		                                                      ->addFieldToFilter('brand_id', ['eq' => $brandId])
		                                                      ->getFirstItem();

		if (!$brandFollower->getId()) {

			$brandFollower->setBrandId($brandId)
			              ->setCustomerId($customerId)
			              ->setIsActive(1);
			$brandFollower->save();
		} else if (!$brandFollower->getIsActive()) {

			$brandFollower->setIsActive(1);
			$brandFollower->save();
		}

		return $this->getSingleBrand($brandId);
	}

	public function removeFollowByToken($brandId, $customerId)
	{
		if ($this->isValidByBrandId($brandId) == self::NOT_FOUND) {

			return "NOT_FOUND CUSTOMER";
		}

		/* @var Follower $brandFollower */
		$brandFollower = $this->brandFollowerCollectionFactory->create()
		                                                      ->addFieldToFilter('customer_id', ['eq' => $customerId])
		                                                      ->addFieldToFilter('brand_id', ['eq' => $brandId])
		                                                      ->addFieldToFilter('is_active', ['eq' => Brand::STATUS_ENABLED])
		                                                      ->getFirstItem();

		if ($brandFollower->getId()) {

			$brandFollower->setIsActive(0);
			$brandFollower->save();
		}

		return $this->getSingleBrand($brandId);
	}

	public function isValidByBrandId($brandId)
	{
		/* @var Brand $brand */
		$brand = $this->brandCollectionFactory->create()
		                                      ->addFieldToFilter('brand_id', ['eq' => $brandId])
		                                      ->getFirstItem();

		if (!$brand->getId()) {

			return self::NOT_FOUND;
		}

		return $brand->getId();
	}

	/**
	 * Convert brand object to API array result
	 *
	 * @param Brand $brand
	 */
	protected function _toBrandArray(Brand $brand, $keys = array())
	{
		$result = $brand->toArray($keys);

		if (!empty($result['logo']) && $brand->getLogo()) { // get image logo url

			$result['logo'] = $brand->getImageLogoUrl();
		}

		if (!empty($result['brand_banner']) && $brand->getBrandBanner()) { // get brand banner url

			$result['brand_banner'] = $brand->getImageBrandBannerUrl();
		}

		if (!empty($result['product_banner']) && $brand->getProductBanner()) { // get product banner url

			$result['product_banner'] = $brand->getImageProductBannerUrl();
		}

		return $result;
	}

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param Collection $collection
     * @return void
     */
    protected function addFilterGroupToCollection(
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        Collection $collection
    )
    {
        $fields = [];
        $categoryFilter = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $conditionType = $filter->getConditionType() ? $filter->getConditionType() : 'eq';

            if ($filter->getField() == 'category_id') {
                $categoryFilter[$conditionType][] = $filter->getValue();
                continue;
            }
            $fields[] = ['attribute' => $filter->getField(), $conditionType => $filter->getValue()];
        }

        if ($categoryFilter) {
            $collection->addCategoriesFilter($categoryFilter);
        }

        if ($fields) {
            $collection->addFieldToFilter($fields);
        }
    }

    public function getFollowingProductList($customerId)
    {
        $brand_product = $this->brandFollowerCollectionFactory->create()
            ->addFieldToFilter('customer_id', ['eq' => $customerId])
            ->addFieldToFilter('is_active', ['eq' => Brand::STATUS_ENABLED])
            ->addFieldToSelect('brand_id');

        $brand_product->join(
            ['brand_product' => 'catalog_product_entity_int'],
            'main_table.brand_id = brand_product.value',
            ['entity_id']);

        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->productCollectionFactory->create()
            ->addIdFilter($brand_product->getColumnValues('entity_id'), false);

        $this->extensionAttributesJoinProcessor->process($collection);

        foreach ($this->metadataServiceInterface->getList($this->searchCriteriaBuilder->create())->getItems() as $metadata) {
            $collection->addAttributeToSelect($metadata->getAttributeCode());
        }

        $collection->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()])
            ->addAttributeToFilter('visibility', ['in' => $this->productVisibility->getVisibleInSiteIds()]);

        //$collection->setCurPage($searchCriteria->getCurrentPage());
        //$collection->setPageSize($searchCriteria->getPageSize());
        $collection->load();

        $searchResult = $this->productSearchResultsFactory->create();
        //$searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());
        return $searchResult;
    }

    public function getFollowingProductListByFilter($customerId, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $brand_product = $this->brandFollowerCollectionFactory->create()
            ->addFieldToFilter('customer_id', ['eq' => $customerId])
            ->addFieldToFilter('is_active', ['eq' => Brand::STATUS_ENABLED])
            ->addFieldToSelect('brand_id');

        $brand_product->join(
            ['brand_product' => 'catalog_product_entity_int'],
            'main_table.brand_id = brand_product.value',
            ['entity_id']);

        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->productCollectionFactory->create()
            ->addIdFilter($brand_product->getColumnValues('entity_id'), false);

        $this->extensionAttributesJoinProcessor->process($collection);

        foreach ($this->metadataServiceInterface->getList($this->searchCriteriaBuilder->create())->getItems() as $metadata) {
            $collection->addAttributeToSelect($metadata->getAttributeCode());
        }

        $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
        $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');

        //Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }

        // @var SortOrder $sortOrder
        foreach ((array)$searchCriteria->getSortOrders() as $sortOrder) {
            $field = $sortOrder->getField();
            $collection->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
            );
        }

        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->load();

        $searchResult = $this->productSearchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());
        return $searchResult;
    }
}