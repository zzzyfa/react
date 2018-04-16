<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 03/04/2018
 * Time: 4:01 PM
 */

namespace Althea\Catalog\Model;

use Magento\Catalog\Model\Product\Gallery\MimeTypeExtensionMap;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Api\Data\ImageContentInterfaceFactory;
use Magento\Framework\Api\ImageContentValidatorInterface;
use Magento\Framework\Api\ImageProcessorInterface;

class ProductRepository extends \Magento\Catalog\Model\ProductRepository {

	protected $_categoryFactory;

	/**
	 * @inheritDoc
	 */
	public function __construct(
		\Magento\Catalog\Model\CategoryFactory $categoryFactory,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper $initializationHelper,
		\Magento\Catalog\Api\Data\ProductSearchResultsInterfaceFactory $searchResultsFactory,
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
		\Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
		\Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository,
		\Magento\Catalog\Model\ResourceModel\Product $resourceModel,
		\Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks $linkInitializer,
		\Magento\Catalog\Model\Product\LinkTypeProvider $linkTypeProvider,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Api\FilterBuilder $filterBuilder,
		\Magento\Catalog\Api\ProductAttributeRepositoryInterface $metadataServiceInterface,
		\Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter,
		\Magento\Catalog\Model\Product\Option\Converter $optionConverter,
		\Magento\Framework\Filesystem $fileSystem,
		ImageContentValidatorInterface $contentValidator,
		ImageContentInterfaceFactory $contentFactory,
		MimeTypeExtensionMap $mimeTypeExtensionMap,
		ImageProcessorInterface $imageProcessor,
		\Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor
	)
	{
		$this->_categoryFactory = $categoryFactory;

		parent::__construct($productFactory, $initializationHelper, $searchResultsFactory, $collectionFactory, $searchCriteriaBuilder, $attributeRepository, $resourceModel, $linkInitializer, $linkTypeProvider, $storeManager, $filterBuilder, $metadataServiceInterface, $extensibleDataObjectConverter, $optionConverter, $fileSystem, $contentValidator, $contentFactory, $mimeTypeExtensionMap, $imageProcessor, $extensionAttributesJoinProcessor);
	}

	/**
	 * @inheritDoc
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

		// althea:
		// - filter product collection by child categories if parent category is anchor
		foreach ($categoryFilter as $condition => $value) {

			$category = $this->_categoryFactory->create()
			                                   ->load(reset($value));

			if ($condition != 'eq' || !$category->getData('is_anchor')) {

				continue;
			}

			$children                   = $category->getAllChildren(true);
			$categoryFilter[$condition] = $children;
		}

        if ($categoryFilter) {
            $collection->addCategoriesFilter($categoryFilter);
        }

        // althea:
		// - filter product collection by current website regardless of status
        $collection->addWebsiteFilter($this->storeManager->getWebsite());

		if ($fields) {
			$collection->addFieldToFilter($fields);
		}
	}

}