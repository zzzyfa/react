<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 08/03/2018
 * Time: 7:26 PM
 */

namespace Althea\ProductFilter\Plugin\Model\Adapter\Mysql\Filter;

use Althea\ProductFilter\Block\Product\ProductList\BestSellers;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Search\Request\FilterInterface;

class PreprocessorPlugin {

	protected $_categoryRepository;

	/**
	 * PreprocessorPlugin constructor.
	 *
	 * @param \Magento\Catalog\Model\CategoryRepository $categoryRepository
	 */
	public function __construct(\Magento\Catalog\Model\CategoryRepository $categoryRepository)
	{
		$this->_categoryRepository = $categoryRepository;
	}

	public function aroundProcess(\Magento\CatalogSearch\Model\Adapter\Mysql\Filter\Preprocessor $subject, \Closure $proceed, FilterInterface $filter, $isNegation, $query)
	{
		try {

			if ($filter->getField() == 'category_ids') {

				$category = $this->_categoryRepository->get((int) $filter->getValue());

				if ($category->getUrlKey() == BestSellers::URL_KEY_BESTSELLERS) {

					return 'category_ids_index.category_id IN ' .  sprintf("(%s,%s)", $category->getParentId(), $category->getId());
				}
			}
		} catch (NoSuchEntityException $e) {

		}

		return $proceed($filter, $isNegation, $query);
	}

}