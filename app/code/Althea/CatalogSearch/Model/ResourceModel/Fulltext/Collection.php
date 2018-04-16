<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 24/03/2018
 * Time: 5:11 PM
 */

namespace Althea\CatalogSearch\Model\ResourceModel\Fulltext;

use Althea\Catalog\Model\Category\Attribute\Source\Sortby;
use Althea\ProductFilter\Block\Product\ProductList\BestSellers;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\CatalogSearch\Model\Search\RequestGenerator;
use Magento\Framework\Api\Search\SearchResultFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Search\Request\EmptyRequestDataException;
use Magento\Framework\Search\Request\NonExistingRequestNameException;
use Magento\Store\Model\Store;

class Collection extends \TemplateMonster\ShopByBrand\Model\ResourceModel\Fulltext\Collection {

	protected $_catalogSession;
	protected $_facetSkipAttributes;
	protected $_facetFilters = [];

	/**
	 * @var string
	 */
	protected $_searchRequestName;

	/**
	 * @var string
	 */
	protected $_facetQueryText;

	/**
	 * @var \Magento\Framework\Api\Search\SearchCriteriaBuilder
	 */
	protected $_facetSearchCriteriaBuilder;

	/**
	 * @var \Magento\Framework\Api\FilterBuilder
	 */
	protected $_facetFilterBuilder;

	/**
	 * @var SearchResultFactory
	 */
	protected $_facetSearchResultFactory;

	/**
	 * @var \Magento\Framework\Api\Search\SearchResultInterface
	 */
	protected $_facetSearchResult;

	/**
	 * @var \Magento\Search\Api\SearchInterface
	 */
	protected $_facetSearch;

	/**
	 * @inheritDoc
	 */
	public function __construct(
		\Magento\Catalog\Model\Session $session,
		\Magento\Framework\Data\Collection\EntityFactory $entityFactory,
		\Psr\Log\LoggerInterface $logger,
		\Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
		\Magento\Framework\Event\ManagerInterface $eventManager,
		\Magento\Eav\Model\Config $eavConfig,
		\Magento\Framework\App\ResourceConnection $resource,
		\Magento\Eav\Model\EntityFactory $eavEntityFactory,
		\Magento\Catalog\Model\ResourceModel\Helper $resourceHelper,
		\Magento\Framework\Validator\UniversalFactory $universalFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Module\Manager $moduleManager,
		\Magento\Catalog\Model\Indexer\Product\Flat\State $catalogProductFlatState,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Catalog\Model\Product\OptionFactory $productOptionFactory,
		\Magento\Catalog\Model\ResourceModel\Url $catalogUrl,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Framework\Stdlib\DateTime $dateTime,
		\Magento\Customer\Api\GroupManagementInterface $groupManagement,
		\Magento\Search\Model\QueryFactory $catalogSearchData,
		\Magento\Framework\Search\Request\Builder $requestBuilder,
		\Magento\Search\Model\SearchEngine $searchEngine,
		\Magento\Framework\Search\Adapter\Mysql\TemporaryStorageFactory $temporaryStorageFactory,
		\Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
		string $searchRequestName = 'catalog_view_container',
		SearchResultFactory $searchResultFactory = null
	)
	{
		$this->_catalogSession      = $session;
		$this->_searchRequestName   = $searchRequestName;
		$this->_facetSkipAttributes = ['brand_althea'];

		parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $eavConfig, $resource, $eavEntityFactory, $resourceHelper, $universalFactory, $storeManager, $moduleManager, $catalogProductFlatState, $scopeConfig, $productOptionFactory, $catalogUrl, $localeDate, $customerSession, $dateTime, $groupManagement, $catalogSearchData, $requestBuilder, $searchEngine, $temporaryStorageFactory, $connection, $searchRequestName, $searchResultFactory);
	}

	/**
	 * @inheritDoc
	 */
	public function addCategoryFilter(\Magento\Catalog\Model\Category $category)
	{
		$this->addFieldToFilter('category_ids', $category->getId());

		// althea:
		// - add root category for bestsellers
		if ($category->getUrlKey() == BestSellers::URL_KEY_BESTSELLERS) {

			$this->_catalogSession->setIsBestSellers(true);
			$this->_productLimitationFilters['category_id'] = [$category->getParentId(), $category->getId()];
		} else {

			$this->_productLimitationFilters['category_id'] = $category->getId();
		}

		if ($category->getIsAnchor()) {
			unset($this->_productLimitationFilters['category_is_anchor']);
		} else {
			$this->_productLimitationFilters['category_is_anchor'] = 1;
		}

		if ($this->getStoreId() == Store::DEFAULT_STORE_ID) {
			$this->_applyZeroStoreProductLimitations();
		} else {
			$this->_applyProductLimitations();
		}

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	protected function _applyProductLimitations()
	{
		$this->_prepareProductLimitationFilters();
		$this->_productLimitationJoinWebsite();
		$this->_productLimitationJoinPrice();
		$filters = $this->_productLimitationFilters;

		if (!isset($filters['category_id']) && !isset($filters['visibility'])) {
			return $this;
		}

		$conditions = [
			'cat_index.product_id=e.entity_id',
			$this->getConnection()->quoteInto('cat_index.store_id=?', $filters['store_id']),
		];
		if (isset($filters['visibility']) && !isset($filters['store_table'])) {
			$conditions[] = $this->getConnection()->quoteInto('cat_index.visibility IN(?)', $filters['visibility']);
		}

		// althea:
		// - check if multiple category ids
		if (is_array($filters['category_id'])) {

			$conditions[] = $this->getConnection()->quoteInto('cat_index.category_id IN(?)', $filters['category_id']);
		} else {

			$conditions[] = $this->getConnection()->quoteInto('cat_index.category_id=?', $filters['category_id']);
		}

		if (isset($filters['category_is_anchor'])) {
			$conditions[] = $this->getConnection()->quoteInto('cat_index.is_parent=?', $filters['category_is_anchor']);
		}

		$joinCond = join(' AND ', $conditions);
		$fromPart = $this->getSelect()->getPart(\Magento\Framework\DB\Select::FROM);
		if (isset($fromPart['cat_index'])) {
			$fromPart['cat_index']['joinCondition'] = $joinCond;
			$this->getSelect()->setPart(\Magento\Framework\DB\Select::FROM, $fromPart);
		} else {
			$this->getSelect()->join(
				['cat_index' => $this->getTable('catalog_category_product_index')],
				$joinCond,
				['cat_index_position' => 'position']
			);
		}

		$this->_productLimitationJoinStore();
		$this->_eventManager->dispatch(
			'catalog_product_collection_apply_limitations_after',
			['collection' => $this]
		);

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function addAttributeToSort($attribute, $dir = ProductCollection::SORT_ORDER_ASC)
	{
		switch ($attribute) {

			case Sortby::SORT_TYPE_POPULAR:
				$this->getSelect()
				     ->joinLeft(
					     ['bestseller' => 'sales_bestsellers_aggregated_yearly'],
					     sprintf('e.entity_id = bestseller.product_id AND bestseller.store_id IN (%s)', $this->getStoreId()),
					     []
				     )
				     ->group('e.entity_id')
				     ->order(sprintf("bestseller.qty_ordered %s", ProductCollection::SORT_ORDER_DESC));

				return $this;
				break;
			case Sortby::SORT_TYPE_NAME_ASC:
				return \Magento\Catalog\Model\ResourceModel\Product\Collection::addAttributeToSort('name', $dir);
				break;
			case Sortby::SORT_TYPE_NAME_DESC:
				return \Magento\Catalog\Model\ResourceModel\Product\Collection::addAttributeToSort('name', ProductCollection::SORT_ORDER_DESC);
				break;
			case Sortby::SORT_TYPE_PRICE_LOWEST:
				return \Magento\Catalog\Model\ResourceModel\Product\Collection::addAttributeToSort('price', $dir);
				break;
			case Sortby::SORT_TYPE_PRICE_HIGHEST:
				return \Magento\Catalog\Model\ResourceModel\Product\Collection::addAttributeToSort('price', ProductCollection::SORT_ORDER_DESC);
				break;
			case Sortby::SORT_TYPE_NEWEST:
				$this->getSelect()
				     ->order("e.created_at {$dir}");

				return $this;
				break;
		}

		return \Magento\Catalog\Model\ResourceModel\Product\Collection::addAttributeToSort($attribute, $dir);
	}

	protected function _getFacetSearchCriteriaBuilder()
	{
		if (!$this->_facetSearchCriteriaBuilder) {

			$this->_facetSearchCriteriaBuilder = clone ObjectManager::getInstance()->get('\Magento\Framework\Api\Search\SearchCriteriaBuilder');
		}

		return $this->_facetSearchCriteriaBuilder;
	}

	protected function _getFacetFilterBuilder()
	{
		if (!$this->_facetFilterBuilder) {

			$this->_facetFilterBuilder = clone ObjectManager::getInstance()->get('\Magento\Framework\Api\FilterBuilder');
		}

		return $this->_facetFilterBuilder;
	}

	protected function _getFacetSearch()
	{
		if (!$this->_facetSearch) {

			$this->_facetSearch = clone ObjectManager::getInstance()->get('\Magento\Search\Api\SearchInterface');
		}

		return $this->_facetSearch;
	}

	/**
	 * @inheritDoc
	 */
	public function addFieldToFilter($field, $condition = null)
	{
		if (!array_key_exists($field, $this->_facetFilters)
			&& !in_array($field, $this->_facetSkipAttributes)
		) {

			$this->_facetFilters[$field] = $condition;
		}

		return parent::addFieldToFilter($field, $condition);
	}

	/**
	 * @inheritDoc
	 */
	public function addSearchFilter($query)
	{
		$this->_facetQueryText = trim($this->_facetQueryText . ' ' . $query);

		return parent::addSearchFilter($query);
	}

	protected function _renderFiltersBefore()
	{
		parent::_renderFiltersBefore();

		$this->_addFieldToFacetFilter();
		$this->_getFacetSearch();

		if ($this->_facetQueryText) {

			$this->_facetFilterBuilder->setField('search_term');
			$this->_facetFilterBuilder->setValue($this->_facetQueryText);
			$this->_facetSearchCriteriaBuilder->addFilter($this->_facetFilterBuilder->create());
		}

		$priceRangeCalculation = $this->_scopeConfig->getValue(
			\Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmFactory::XML_PATH_RANGE_CALCULATION,
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);

		if ($priceRangeCalculation) {

			$this->_facetFilterBuilder->setField('price_dynamic_algorithm');
			$this->_facetFilterBuilder->setValue($priceRangeCalculation);
			$this->_facetSearchCriteriaBuilder->addFilter($this->_facetFilterBuilder->create());
		}

		$searchCriteria = $this->_facetSearchCriteriaBuilder->create();
		$searchCriteria->setRequestName($this->_searchRequestName);

		try {

			$this->_facetSearchResult = $this->_getFacetSearch()->search($searchCriteria);
		} catch (EmptyRequestDataException $e) {

			/** @var \Magento\Framework\Api\Search\SearchResultInterface $searchResult */
			$this->_facetSearchResult = $this->_facetSearchResultFactory->create()->setItems([]);
		} catch (NonExistingRequestNameException $e) {

			$this->_logger->error($e->getMessage());
			throw new LocalizedException(__('Sorry, something went wrong. You can find out more in the error log.'));
		}
	}

	/**
	 * @inheritDoc
	 */
	public function getFacetedData($field)
	{
		$this->_renderFilters();

		$result       = [];
		$aggregations = $this->_facetSearchResult->getAggregations();

		// This behavior is for case with empty object when we got EmptyRequestDataException
		if (null !== $aggregations) {

			$bucket = $aggregations->getBucket($field . RequestGenerator::BUCKET_SUFFIX);

			if ($bucket) {

				foreach ($bucket->getValues() as $value) {
					$metrics = $value->getMetrics();
					$result[$metrics['value']] = $metrics;
				}
			} else {

				throw new StateException(__('Bucket does not exist'));
			}
		}

		return $result;
	}

	protected function _addFieldToFacetFilter()
	{
		$this->_getFacetSearchCriteriaBuilder();
		$this->_getFacetFilterBuilder();

		foreach ($this->_facetFilters as $field => $condition) {

			if ($this->_facetSearchResult !== null) {

				throw new \RuntimeException('Illegal state');
			}

			if (!is_array($condition) || !in_array(key($condition), ['from', 'to'])) {

				$this->_facetFilterBuilder->setField($field);
				$this->_facetFilterBuilder->setValue($condition);
				$this->_facetSearchCriteriaBuilder->addFilter($this->_facetFilterBuilder->create());
			} else {

				if (!empty($condition['from'])) {

					$this->_facetFilterBuilder->setField("{$field}.from");
					$this->_facetFilterBuilder->setValue($condition['from']);
					$this->_facetSearchCriteriaBuilder->addFilter($this->_facetFilterBuilder->create());
				}

				if (!empty($condition['to'])) {

					$this->_facetFilterBuilder->setField("{$field}.to");
					$this->_facetFilterBuilder->setValue($condition['to']);
					$this->_facetSearchCriteriaBuilder->addFilter($this->_facetFilterBuilder->create());
				}
			}
		}
	}

}