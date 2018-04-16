<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 19/03/2018
 * Time: 4:16 PM
 */

namespace Althea\Catalog\Setup;

use Althea\Catalog\Model\Category\Attribute\Source\Sortby;
use Magento\Catalog\Api\Data\CategoryAttributeInterface;
use Magento\Catalog\Model\Category;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface {

	protected $_attributeFactory;
	protected $_catalogConfig;

	/**
	 * UpgradeData constructor.
	 *
	 * @param \Magento\Eav\Model\Entity\AttributeFactory $attributeFactory
	 * @param \Althea\Catalog\Model\Config               $config
	 */
	public function __construct(
		\Magento\Eav\Model\Entity\AttributeFactory $attributeFactory,
		\Althea\Catalog\Model\Config $config
	)
	{
		$this->_attributeFactory = $attributeFactory;
		$this->_catalogConfig    = $config;
	}

	/**
	 * @inheritDoc
	 */
	public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
	{
		$setup->startSetup();

		if (version_compare($context->getVersion(), '1.0.2', '<')) {

			$this->_upgradeCategoryUseCustomSortTypes($setup);
		}

		$setup->endSetup();
	}

	protected function _upgradeCategoryUseCustomSortTypes(ModuleDataSetupInterface $setup)
	{
		try {

			$sortBy = $this->_attributeFactory->create()
			                                  ->loadByCode(CategoryAttributeInterface::ENTITY_TYPE_CODE, Category::KEY_AVAILABLE_SORT_BY);

			$setup->getConnection()
			      ->update(
				      $sortBy->getBackendTable(),
				      ['value' => $this->_getCustomSortTypes()],
				      ['attribute_id = ?' => $sortBy->getId()]
			      );

			$defaultSortBy = $this->_attributeFactory->create()
			                                         ->loadByCode(CategoryAttributeInterface::ENTITY_TYPE_CODE, 'default_sort_by');

			$setup->getConnection()
			      ->delete(
				      $defaultSortBy->getBackendTable(),
				      ['attribute_id = ?' => $defaultSortBy->getId()]
			      );
		} catch (LocalizedException $e) {

		}
	}

	protected function _getCustomSortTypes()
	{
		return implode(",", array_keys($this->_catalogConfig->getCustomAttributeUsedForSortByArray()));
	}

}