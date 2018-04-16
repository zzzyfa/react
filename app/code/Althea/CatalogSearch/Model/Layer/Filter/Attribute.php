<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 22/03/2018
 * Time: 3:06 PM
 */

namespace Althea\CatalogSearch\Model\Layer\Filter;

/**
 * Class Attribute
 *
 * - based on Smile-SA/elasticsuite for reference
 *
 * @package Althea\CatalogSearch\Model\Layer\Filter
 */
class Attribute extends \Magento\CatalogSearch\Model\Layer\Filter\Attribute implements FilterInterface {

	const SORT_ORDER_MANUAL = "_manual";

	/**
	 * @var array
	 */
	protected $currentFilterValue = [];

	/**
	 * @var \Magento\Framework\Filter\StripTags
	 */
	private $tagFilter;

	/**
	 * @var boolean
	 */
	private $hasMoreItems = false;

	/**
	 * @var \Althea\CatalogSearch\Helper\Mapping
	 */
	private $mappingHelper;

	/**
	 * Constructor.
	 *
	 * @param \Magento\Catalog\Model\Layer\Filter\ItemFactory      $filterItemFactory Factory for item of the facets.
	 * @param \Magento\Store\Model\StoreManagerInterface           $storeManager      Store manager.
	 * @param \Magento\Catalog\Model\Layer                         $layer             Catalog product layer.
	 * @param \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder   Item data builder.
	 * @param \Magento\Framework\Filter\StripTags                  $tagFilter         String HTML tags filter.
	 * @param \Althea\CatalogSearch\Helper\Mapping                 $mappingHelper     Mapping helper.
	 * @param array                                                $data              Custom data.
	 */
	public function __construct(
		\Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Catalog\Model\Layer $layer,
		\Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
		\Magento\Framework\Filter\StripTags $tagFilter,
		\Althea\CatalogSearch\Helper\Mapping $mappingHelper,
		array $data = []
	) {
		parent::__construct(
			$filterItemFactory,
			$storeManager,
			$layer,
			$itemDataBuilder,
			$tagFilter,
			$data
		);

		$this->tagFilter     = $tagFilter;
		$this->mappingHelper = $mappingHelper;
	}

	/**
	 * {@inheritDoc}
	 */
	public function apply(\Magento\Framework\App\RequestInterface $request)
	{
		$attributeValue = $request->getParam($this->_requestVar);

		if (empty($attributeValue)) {

			return $this;
		} else if (!is_array($attributeValue)) {

			$attributeValue = [$attributeValue];
		}

		$this->currentFilterValue = $attributeValue;
		$attribute                = $this->getAttributeModel();

		/** @var \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $productCollection */
		$productCollection = $this->getLayer()
		                          ->getProductCollection();
		$productCollection->addFieldToFilter($attribute->getAttributeCode(), ['in' => $attributeValue]);
		$label = $this->getOptionText(implode(",", $attributeValue));

		if (is_array($label)) {

			$label = implode(", ", $label);
		}

		$this->getLayer()
		     ->getState()
		     ->addFilter($this->_createItem($label, $this->currentFilterValue));

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function addFacetToCollection($config = [])
	{
		$facetField  = $this->getFilterField();
		$facetType   = \Magento\Framework\Search\Request\BucketInterface::TYPE_TERM;
		$facetConfig = $this->getFacetConfig($config);

		$productCollection = $this->getLayer()->getProductCollection();
		$productCollection->addFacet($facetField, $facetType, $facetConfig);

		return $this;
	}

	/**
	 * Indicates if the facets has more documents to be displayed.
	 *
	 * @return boolean
	 */
	public function hasMoreItems()
	{
		return $this->hasMoreItems;
	}

	/**
	 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
	 * @SuppressWarnings(PHPMD.ElseExpression)
	 *
	 * {@inheritDoc}
	 */
	protected function _getItemsData()
	{
		$attribute = $this->getAttributeModel();
		/** @var \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $productCollection */
		$productCollection = $this->getLayer()
		                          ->getProductCollection();
		$optionsFacetedData = $productCollection->getFacetedData($attribute->getAttributeCode());

		if (count($optionsFacetedData) === 0
			&& $this->getAttributeIsFilterable($attribute) !== static::ATTRIBUTE_OPTIONS_ONLY_WITH_RESULTS
		) {
			return $this->itemDataBuilder->build();
		}

		$productSize = $productCollection->getSize();

		$options = $attribute->getFrontend()
		                     ->getSelectOptions();
		foreach ($options as $option) {
			if (empty($option['value'])) {
				continue;
			}

			$value = $option['value'];

			$count = isset($optionsFacetedData[$value]['count'])
				? (int)$optionsFacetedData[$value]['count']
				: 0;
			// Check filter type
//			if (
//				$this->getAttributeIsFilterable($attribute) === static::ATTRIBUTE_OPTIONS_ONLY_WITH_RESULTS
//                && (!$this->isOptionReducesResults($count, $productSize) || $count === 0)
//			) {
//				continue;
//			}

			// althea:
			// - show filter facet even if product count is less than filter facet count
			if ($this->getAttributeIsFilterable($attribute) === static::ATTRIBUTE_OPTIONS_ONLY_WITH_RESULTS
				&& $count === 0
			) {
				continue;
			}
			$this->itemDataBuilder->addItemData(
				$this->tagFilter->filter($option['label']),
				$value,
				$count
			);
		}

		$data  = $this->itemDataBuilder->build();
		$items = [];

		foreach ($data as $item) {

			$items[$item['value']] = $item;
		}

		$items = $this->addOptionsData($items);

		return $items;
	}

	/**
	 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
	 * @SuppressWarnings(PHPMD.ElseExpression)
	 *
	 * {@inheritDoc}
	 */
	protected function _initItems()
	{
		parent::_initItems();

		foreach ($this->_items as $item) {
			$applyValue = $item->getValue();
			if (($valuePos = array_search($applyValue, $this->currentFilterValue)) !== false) {
				$item->setIsSelected(true);
				$applyValue = $this->currentFilterValue;
				unset($applyValue[$valuePos]);
			} else {
				$applyValue = array_merge($this->currentFilterValue, [$applyValue]);
			}

			$item->setApplyFilterValue(array_values($applyValue));
		}

		return $this;
	}

	/**
	 * Retrieve ES filter field.
	 *
	 * @return string
	 */
	protected function getFilterField()
	{
		$field = $this->getAttributeModel()->getAttributeCode();

		if ($this->getAttributeModel()->usesSource()) {
			$field = $this->mappingHelper->getOptionTextFieldName($field);
		}

		return $field;
	}

	/**
	 * Retrieve configuration of the facet added to the collection.
	 *
	 * @param array $config Config override.
	 *
	 * @return array
	 */
	private function getFacetConfig($config = [])
	{
		$attribute = $this->getAttributeModel();

		$defaultConfig = [
			'size'      => $this->getFacetSize(),
			'sortOrder' => $attribute->getFacetSortOrder(),
		];

		return array_merge($defaultConfig, $config);
	}

	/**
	 * Current facet size.
	 *
	 * @return integer
	 */
	private function getFacetSize()
	{
		$attribute = $this->getAttributeModel();
		$size      = (int) $attribute->getFacetMaxSize();

		$hasValue      = !empty($this->currentFilterValue);
		$isManualOrder = $attribute->getFacetSortOrder() == self::SORT_ORDER_MANUAL;

		if ($hasValue || $isManualOrder) {
			$size = 0;
		}

		return $size;
	}

	/**
	 * Resort items according option position defined in admin.
	 *
	 * @param array $items Items to be sorted.
	 *
	 * @return array
	 */
	private function addOptionsData(array $items)
	{
		if ($this->getAttributeModel()->getFacetSortOrder() == self::SORT_ORDER_MANUAL) {
			$options = $this->getAttributeModel()->getFrontend()->getSelectOptions();
			$optionPosition = 0;

			if (!empty($options)) {
				foreach ($options as $option) {
					if (isset($option['label'])) {
						$optionLabel = (string) $option['label'];
						$optionPosition++;

						if ($optionLabel !== null && isset($items[$optionLabel])) {
							$items[$optionLabel]['adminSortIndex'] = $optionPosition;
							$items[$optionLabel]['value']          = $option['value'];
						}
					}
				}
			}

			usort($items, function ($item1, $item2) {
				return $item1['adminSortIndex'] <= $item2['adminSortIndex'] ? -1 : 1;
			});
		}

		return $items;
	}

}