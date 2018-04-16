<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 14/12/2017
 * Time: 5:19 PM
 */

namespace Althea\Catalog\Plugin\Category;

use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Eav\Model\Entity\Attribute\Option;

class Tree {

	protected $_extensionAttributeFactory;
	protected $_attributeOptionFactory;
	protected $_categoryFactory;

	/**
	 * CategoryManagement constructor.
	 *
	 * @param \Magento\Catalog\Api\Data\CategoryExtensionFactory $extensionAttributeFactory
	 * @param \Magento\Eav\Model\Entity\Attribute\OptionFactory  $optionFactory
	 * @param \Magento\Catalog\Model\CategoryFactory             $categoryFactory
	 */
	public function __construct(
		\Magento\Catalog\Api\Data\CategoryExtensionFactory $extensionAttributeFactory,
		\Magento\Eav\Model\Entity\Attribute\OptionFactory $optionFactory,
		\Magento\Catalog\Model\CategoryFactory $categoryFactory
	)
	{
		$this->_extensionAttributeFactory = $extensionAttributeFactory;
		$this->_attributeOptionFactory    = $optionFactory;
		$this->_categoryFactory           = $categoryFactory;
	}

	public function aroundGetTree(\Magento\Catalog\Model\Category\Tree $subject, \Closure $proceed, \Magento\Framework\Data\Tree\Node $node, $depth = null, $currentLevel = 0)
	{
		$result        = $proceed($node, $depth, $currentLevel);
		$extAttributes = $result->getExtensionAttributes();

		if (!$extAttributes) {

			$extAttributes = $this->_extensionAttributeFactory->create();
		}

		$sortByOptions     = [];
		$category          = $this->_categoryFactory->create()->load($node->getId());
		$productCollection = $category->getProductCollection()
		                              ->addPriceData();
		$options           = $category->getAvailableSortByOptions();
		$availableOptions  = $category->getAvailableSortBy();

		foreach ($options as $key => $val) {

			/* @var Option $option */
			$option = $this->_attributeOptionFactory->create();

			$option->setLabel($val);
			$option->setValue($key);

			$sortByOptions[] = $option;
		}

		// filter available sort by type
		foreach ($sortByOptions as $key => $val) {

			if ($availableOptions && !in_array($val->getValue(), $availableOptions)) {

				unset($sortByOptions[$key]);
			}
		}

		$extAttributes->setSortByOptions($sortByOptions);

		// get minimum price in category
		$minPrice = $productCollection->getMinPrice();

		$extAttributes->setMinPrice($minPrice);

		// get maximum price in category
		$maxPrice = $productCollection->getMaxPrice();

		$extAttributes->setMaxPrice($maxPrice);

		$extAttributes->setIncludeInMenu($category->getIncludeInMenu() == 1);

		// set extension attributes
		$result->setExtensionAttributes($extAttributes);

		return $result;
	}

}