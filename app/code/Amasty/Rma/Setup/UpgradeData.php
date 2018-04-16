<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Setup;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'allow_for_rma',
            [
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Allow for RMA',
                'input' => 'select',
                'class' => '',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '1',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'unique' => false,
                'apply_to' => ''
            ]
        );
        $attributeId = $eavSetup->getAttributeId(
            \Magento\Catalog\Model\Product::ENTITY,
            'allow_for_rma'
        );

        foreach (
            $eavSetup->getAllAttributeSetIds(
                \Magento\Catalog\Model\Product::ENTITY
            ) as $attributeSetId
        ) {
            try {
                $attributeGroupId = $eavSetup->getAttributeGroupId(
                    \Magento\Catalog\Model\Product::ENTITY,
                    $attributeSetId,
                    'General'
                );
            } catch (\Exception $e) {
                $attributeGroupId = $eavSetup->getDefaultAttributeGroupId(
                    \Magento\Catalog\Model\Product::ENTITY,
                    $attributeSetId
                );
            }
            $eavSetup->addAttributeToSet(
                \Magento\Catalog\Model\Product::ENTITY,
                $attributeSetId,
                $attributeGroupId,
                $attributeId
            );
        }

        $setup->endSetup();
    }
}
