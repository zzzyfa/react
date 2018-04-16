<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 20/03/2018
 * Time: 9:48 AM
 */

namespace Althea\Catalog\Plugin\Model\Category\Attribute;

use Magento\Catalog\Model\Category;

class OptionManagementPlugin {

	protected $_catalogConfig;

	/**
	 * OptionManagementPlugin constructor.
	 *
	 * @param \Althea\Catalog\Model\Config $config
	 */
	public function __construct(\Althea\Catalog\Model\Config $config)
	{
		$this->_catalogConfig = $config;
	}

	public function aroundGetItems(\Magento\Catalog\Model\Category\Attribute\OptionManagement $subject, \Closure $proceed, $attributeCode)
	{
		$result = $proceed($attributeCode);

		// althea:
		// - filter available_sort_by options to use only custom sort options
		if ($attributeCode == Category::KEY_AVAILABLE_SORT_BY) {

			$result = array_filter($result, [$this, 'filterSortOptions']);
		}

		return $result;
	}

	public function filterSortOptions(\Magento\Eav\Api\Data\AttributeOptionInterface $attributeOption)
	{
		$customOptions = $this->_catalogConfig->getCustomAttributeUsedForSortByArray();

		return array_key_exists($attributeOption->getValue(), $customOptions);
	}

}