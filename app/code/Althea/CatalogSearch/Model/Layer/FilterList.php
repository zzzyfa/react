<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 22/03/2018
 * Time: 3:01 PM
 */

namespace Althea\CatalogSearch\Model\Layer;

/**
 * Class FilterList
 *
 * - based on Smile-SA/elasticsuite for reference
 *
 * @package Althea\CatalogSearch\Model\Layer
 */
class FilterList extends \Magento\Catalog\Model\Layer\FilterList {

	/**
	 * Boolean filter name
	 */
	const BOOLEAN_FILTER = 'boolean';

	/**
	 * {@inheritDoc}
	 */
	protected function getAttributeFilterClass(\Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute)
	{
		$filterClassName = parent::getAttributeFilterClass($attribute);

		if ($attribute->getBackendType() == 'varchar' && $attribute->getFrontendClass() == 'validate-number') {
			$filterClassName = $this->filterTypes[self::DECIMAL_FILTER];
		}

		if (($attribute->getFrontendInput() == 'boolean')
			&& ($attribute->getSourceModel() == 'Magento\Eav\Model\Entity\Attribute\Source\Boolean')
			&& isset($this->filterTypes[self::BOOLEAN_FILTER])) {
			$filterClassName = $this->filterTypes[self::BOOLEAN_FILTER];
		}

		return $filterClassName;
	}

}