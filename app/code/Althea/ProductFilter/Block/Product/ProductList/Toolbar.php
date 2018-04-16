<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 08/03/2018
 * Time: 10:20 AM
 */

namespace Althea\ProductFilter\Block\Product\ProductList;

class Toolbar extends \Magento\Catalog\Block\Product\ProductList\Toolbar {

	/**
	 * @inheritDoc
	 */
	public function getPagerHtml()
	{
		$pagerBlock = $this->getChildBlock('product_list_toolbar_pager');

		// althea:
		// - Magento cannot find child block of whose parent block has been removed
		// - old parent block: category.products.list
		if (!$pagerBlock) {

			$pagerBlock = $this->getChildBlock('althea_product_list_toolbar_pager');
		}

		if ($pagerBlock instanceof \Magento\Framework\DataObject) {
			/* @var $pagerBlock \Magento\Theme\Block\Html\Pager */
			$pagerBlock->setAvailableLimit($this->getAvailableLimit());

			$pagerBlock->setUseContainer(
				false
			)->setShowPerPage(
				false
			)->setShowAmounts(
				false
			)->setFrameLength(
				$this->_scopeConfig->getValue(
					'design/pagination/pagination_frame',
					\Magento\Store\Model\ScopeInterface::SCOPE_STORE
				)
			)->setJump(
				$this->_scopeConfig->getValue(
					'design/pagination/pagination_frame_skip',
					\Magento\Store\Model\ScopeInterface::SCOPE_STORE
				)
			)->setLimit(
				$this->getLimit()
			)->setCollection(
				$this->getCollection()
			);

			return $pagerBlock->toHtml();
		}

		return '';
	}

}