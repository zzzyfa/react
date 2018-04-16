<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 23/03/2018
 * Time: 3:20 PM
 */

namespace Althea\CatalogSearch\Model\Layer\Filter;

class Price extends \Magento\Catalog\Model\Layer\Filter\Price {

	/**
	 * @var array
	 */
	protected $currentFilterValue = [];

	/**
	 * @var \Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmFactory
	 */
	private $algorithmFactory;

	/**
	 * @var \Magento\Catalog\Model\Layer\Filter\DataProvider\Price
	 */
	private $dataProvider;

	/**
	 * Constructor.
	 *
	 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
	 *
	 * @param \Magento\Catalog\Model\Layer\Filter\ItemFactory               $filterItemFactory   Item filter facotory.
	 * @param \Magento\Store\Model\StoreManagerInterface                    $storeManager        Store manager.
	 * @param \Magento\Catalog\Model\Layer                                  $layer               Search layer.
	 * @param \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder          $itemDataBuilder     Item data builder.
	 * @param \Magento\Catalog\Model\ResourceModel\Layer\Filter\Price       $resource            Price resource.
	 * @param \Magento\Customer\Model\Session                               $customerSession     Customer session.
	 * @param \Magento\Framework\Search\Dynamic\Algorithm                   $priceAlgorithm      Price algorithm.
	 * @param \Magento\Framework\Pricing\PriceCurrencyInterface             $priceCurrency       Price currency.
	 * @param \Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmFactory  $algorithmFactory    Algorithm factory.
	 * @param \Magento\Catalog\Model\Layer\Filter\DataProvider\PriceFactory $dataProviderFactory Data provider.
	 * @param array                                                         $data                Custom data.
	 */
	public function __construct(
		\Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Catalog\Model\Layer $layer,
		\Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
		\Magento\Catalog\Model\ResourceModel\Layer\Filter\Price $resource,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Framework\Search\Dynamic\Algorithm $priceAlgorithm,
		\Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
		\Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmFactory $algorithmFactory,
		\Magento\Catalog\Model\Layer\Filter\DataProvider\PriceFactory $dataProviderFactory,
		array $data = []
	) {
		parent::__construct(
			$filterItemFactory,
			$storeManager,
			$layer,
			$itemDataBuilder,
			$resource,
			$customerSession,
			$priceAlgorithm,
			$priceCurrency,
			$algorithmFactory,
			$dataProviderFactory,
			$data
		);

		$this->algorithmFactory = $algorithmFactory;
		$this->dataProvider     = $dataProviderFactory->create(['layer' => $this->getLayer()]);
	}

	/**
	 * @inheritDoc
	 */
	public function apply(\Magento\Framework\App\RequestInterface $request)
	{
		/**
		 * Filter must be string: $fromPrice-$toPrice
		 */
		$filterParams = $request->getParam($this->getRequestVar());

		if (!$filterParams) {

			return $this;
		} else if (!is_array($filterParams)) {

			$filterParams = [$filterParams];
		}

		//validate filter
		foreach ($filterParams as $filter) {

			$filter = $this->dataProvider->validateFilter($filter);

			if (!$filter) {

				return $this;
			}
		}

		$this->currentFilterValue = $filterParams;

		$this->dataProvider->setPriorIntervals($filterParams);

		$interval = array_map(function ($item) {
			return explode("-", $item);
		}, $filterParams);

		$this->dataProvider->setInterval($interval);
		$this->_applyPriceRange();

		foreach ($filterParams as $filter) {

			list($from, $to) = explode("-", $filter);

			$this->getLayer()
			     ->getState()
			     ->addFilter(
				     $this->_createItem($this->_renderRangeLabel(empty($from) ? 0 : $from, $to), $filter)
			     );
		}

		$abc = (string) $this->getLayer()->getProductCollection()->getSelect();

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function getResetValue()
	{
		$this->dataProvider->getResetValue();
	}

	/**
	 * @inheritDoc
	 */
	public function getClearLinkText()
	{
		if ($this->dataProvider->getPriorIntervals()
		) {
			return __('Clear Price');
		}

		return \Magento\Catalog\Model\Layer\Filter\AbstractFilter::getClearLinkText();
	}

	/**
	 * @inheritDoc
	 */
	protected function _renderRangeLabel($fromPrice, $toPrice)
	{
		$formattedFromPrice = $this->priceCurrency->format($fromPrice);
		if ($toPrice === '') {
			return __('%1 and above', $formattedFromPrice);
		} elseif ($fromPrice == $toPrice && $this->dataProvider->getOnePriceIntervalValue()
		) {
			return $formattedFromPrice;
		} else {
			if ($fromPrice != $toPrice) {
				$toPrice -= .01;
			}

			return __('%1 - %2', $formattedFromPrice, $this->priceCurrency->format($toPrice));
		}
	}

	/**
	 * @inheritDoc
	 */
	protected function __getAdditionalRequestData()
	{
		$result = '';
		$appliedInterval = $this->dataProvider->getInterval();
		if ($appliedInterval) {
			$result = ',' . $appliedInterval[0] . '-' . $appliedInterval[1];
			$priorIntervals = $this->getResetValue();
			if ($priorIntervals) {
				$result .= ',' . $priorIntervals;
			}
		}

		return $result;
	}

	/**
	 * @inheritDoc
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
	 * @inheritDoc
	 */
	protected function _getItemsData()
	{
		$algorithm = $this->algorithmFactory->create();

		return $algorithm->getItemsData([], $this->dataProvider->getAdditionalRequestData());
	}

	/**
	 * @inheritDoc
	 */
	protected function _applyPriceRange()
	{
		$this->dataProvider->getResource()->applyPriceRange($this, $this->dataProvider->getInterval());

		return $this;
	}

}