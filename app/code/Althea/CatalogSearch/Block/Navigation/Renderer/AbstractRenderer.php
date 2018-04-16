<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 22/03/2018
 * Time: 11:47 AM
 */

namespace Althea\CatalogSearch\Block\Navigation\Renderer;

use Magento\LayeredNavigation\Block\Navigation\FilterRendererInterface;
use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\Framework\View\Element\Template;

/**
 * Class AbstractRenderer
 *
 * - based on Smile-SA/elasticsuite for reference
 *
 * @package Althea\CatalogSearch\Block\Navigation\Renderer
 */
abstract class AbstractRenderer extends Template implements FilterRendererInterface {

	/**
	 * @var FilterInterface
	 */
	private $filter;

	/**
	 * {@inheritDoc}
	 */
	public function render(FilterInterface $filter)
	{
		$html         = '';
		$this->filter = $filter;

		if ($this->canRenderFilter()) {
			$this->assign('filterItems', $filter->getItems());
			$html = $this->_toHtml();
			$this->assign('filterItems', []);
		}

		return $html;
	}

	/**
	 * @return FilterInterface
	 */
	public function getFilter()
	{
		return $this->filter;
	}

	/**
	 * Check if the current block can render a filter (previously set through ::setFilter).
	 *
	 * @return boolean
	 */
	abstract protected function canRenderFilter();

}