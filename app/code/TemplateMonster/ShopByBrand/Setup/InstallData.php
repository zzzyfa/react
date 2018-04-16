<?php

namespace TemplateMonster\ShopByBrand\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Catalog\Model\Product;

class InstallData implements InstallDataInterface
{
    protected $_productSetupFactory;

    public function __construct(
        CategorySetupFactory $productSetupFactory
    )
    {
        $this->_productSetupFactory = $productSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $productSetup = $this->_productSetupFactory->create(['setup' => $setup]);

        $productSetup->removeAttribute(Product::ENTITY, 'brand_id');

        $productSetup->addAttribute(Product::ENTITY, 'brand_id', [
                'type' => 'int',
                'label' => 'Brand ID',
                'input' => 'select',
                'source' => 'TemplateMonster\ShopByBrand\Model\Entity\Attribute\Source\Brands',
                'required' => false,
                'sort_order' => 44,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Shop by Brand',
                'used_in_product_listing' => true,
            ]
        );

        $installer->endSetup();
    }
}