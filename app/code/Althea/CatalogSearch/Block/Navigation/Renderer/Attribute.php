<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 22/03/2018
 * Time: 11:46 AM
 */

namespace Althea\CatalogSearch\Block\Navigation\Renderer;

use \Magento\Framework\Exception\LocalizedException;

/**
 * Class Attribute
 *
 * - based on Smile-SA/elasticsuite for reference
 *
 * @package Althea\CatalogSearch\Block\Navigation\Renderer
 */
class Attribute extends AbstractRenderer {

	const JS_COMPONENT = 'Althea_CatalogSearch/js/attribute-filter';

	/**
	 * Returns true if checkox have to be enabled.
	 *
	 * @return boolean
	 */
	public function isMultipleSelectEnabled()
	{
		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getJsLayout()
	{
		$filterItems = $this->getFilter()->getItems();

		try {

			$facetMaxSize = (int)$this->getFilter()->getAttributeModel()->getFacetMaxSize();
		} catch (LocalizedException $e) {

			$facetMaxSize = 10;
		}

		$jsLayoutConfig = [
			'component'    => self::JS_COMPONENT,
			'maxSize'      => $facetMaxSize,
			'hasMoreItems' => (bool)$this->getFilter()->hasMoreItems(),
			'ajaxLoadUrl'  => $this->getAjaxLoadUrl(),
		];

		foreach ($filterItems as $item) {
			$jsLayoutConfig['items'][] = $item->toArray(['label', 'count', 'url', 'is_selected']);
		}

		return json_encode($jsLayoutConfig);
	}

	/**
	 * {@inheritDoc}
	 */
	protected function canRenderFilter()
	{
		return true;
	}

	/**
	 * Get the AJAX load URL (used by the show more and the search features).
	 *
	 * @return string
	 */
	private function getAjaxLoadUrl()
	{
		$qsParams = ['filterName' => $this->getFilter()->getRequestVar()];

		$currentCategory = $this->getFilter()->getLayer()->getCurrentCategory();

		if ($currentCategory && $currentCategory->getId() && $currentCategory->getLevel() > 1) {
			$qsParams['cat'] = $currentCategory->getId();
		}

		$urlParams = ['_current' => true, '_query' => $qsParams];

		return $this->_urlBuilder->getUrl('catalog/navigation_filter/ajax', $urlParams);
	}

}