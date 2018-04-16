<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 22/03/2018
 * Time: 11:42 AM
 */

namespace Althea\CatalogSearch\Block\Navigation;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\LayeredNavigation\Block\Navigation\FilterRendererInterface;
use Magento\Catalog\Model\Layer\Filter\FilterInterface;

/**
 * Class FilterRenderer
 *
 * - based on Smile-SA/elasticsuite for reference
 *
 * @package Althea\CatalogSearch\Block\Navigation
 */
class FilterRenderer extends AbstractBlock implements FilterRendererInterface {

	/**
	 * {@inheritDoc}
	 */
	public function render(FilterInterface $filter)
	{
		$this->setFilter($filter);

		return $this->_toHtml();
	}

	/**
	 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
	 *
	 * {@inheritDoc}
	 */
	public function _toHtml()
	{
		$html = '';

		foreach ($this->getChildNames() as $childName) {
			if ($html === '') {
				$renderer = $this->getChildBlock($childName);
				$html = $renderer->render($this->getFilter());
			}
		}

		return $html;
	}

}