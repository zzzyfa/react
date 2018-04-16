<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 22/03/2018
 * Time: 11:56 AM
 */

namespace Althea\CatalogSearch\Block\Navigation\Renderer;


class Category extends AbstractRenderer {

	/**
	 * Returns true if checkox have to be enabled.
	 *
	 * @return boolean
	 */
	public function isMultipleSelectEnabled()
	{
		return false;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function canRenderFilter()
	{
		return $this->getFilter() instanceof \Magento\CatalogSearch\Model\Layer\Filter\Category;
	}

}