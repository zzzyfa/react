<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 09/04/2018
 * Time: 5:15 PM
 */

namespace Althea\Ui\Component\MassAction;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProviderInterface;

class Filter extends \Magento\Ui\Component\MassAction\Filter {

	/**
	 * @var DataProviderInterface
	 */
	protected $_dataProvider;

	/**
	 * Temp. fix for issue https://github.com/magento/magento2/issues/4231 before upgrading to Magento 2.2.X
	 *
	 * @inheritDoc
	 */
	public function getCollection(AbstractDb $collection)
	{
		$selected = $this->request->getParam(static::SELECTED_PARAM);
		$excluded = $this->request->getParam(static::EXCLUDED_PARAM);
		$isExcludedIdsValid = (is_array($excluded) && !empty($excluded));
		$isSelectedIdsValid = (is_array($selected) && !empty($selected));
		if ('false' !== $excluded) {
			if (!$isExcludedIdsValid && !$isSelectedIdsValid) {
				throw new LocalizedException(__('Please select item(s).'));
			}
		}
		/** @var \Magento\Customer\Model\ResourceModel\Customer\Collection $collection */
		$idsArray = $this->_getFilterIds();
		if (!empty($idsArray)) {
			$collection->addFieldToFilter(
				$collection->getIdFieldName(),
				['in' => $idsArray]
			);
		}
		return $collection;
	}

	/**
	 * Get data provider
	 *
	 * @return DataProviderInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	protected function _getDataProvider()
	{
		if (!$this->_dataProvider) {
			$component = $this->getComponent();
			$this->prepareComponent($component);
			$this->_dataProvider = $component->getContext()->getDataProvider();
		}
		return $this->_dataProvider;
	}

	/**
	 * Get filter ids as array
	 *
	 * @return array|int[]
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	protected function _getFilterIds()
	{
		$idsArray = [];
		$this->applySelectionOnTargetProvider();
		if ($this->_getDataProvider() instanceof \Magento\Ui\DataProvider\AbstractDataProvider) {
			// Use collection's getAllIds for optimization purposes.
			$idsArray = $this->_getDataProvider()->getAllIds();
		} else {
			$dataProvider = $this->_getDataProvider();
			$dataProvider->setLimit(0, false);
			$searchResult = $dataProvider->getSearchResult();
			// Use compatible search api getItems when searchResult is not a collection.
			foreach ($searchResult->getItems() as $item) {
				/** @var $item \Magento\Framework\Api\Search\DocumentInterface */
				$idsArray[] = $item->getId();
			}
		}
		return  $idsArray;
	}

}