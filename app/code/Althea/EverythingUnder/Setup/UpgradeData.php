<?php
namespace Althea\EverythingUnder\Setup;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{

    private $eavSetupFactory;
    protected $eavSetup;

    public function __construct(
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.0') < 0) {
            $this->addWebsiteIdAttribute($setup);
        }

        $setup->endSetup();
    }

    public function addWebsiteIdAttribute(ModuleDataSetupInterface $setup)
    {
        /* @var CustomerSetup $customerSetup */
        $this->eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        // remove previously set attribute
        $this->eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'product_website');
        try {
            $this->addAttribute('product_website', [
                'backend_type' => 'int',
                'frontend_input' => 'select',
                'note' => '',
                'source_model' => 'Magento\Customer\Model\Customer\Attribute\Source\Website',
                'backend_model' => 'Magento\Customer\Model\Customer\Attribute\Backend\Website',
                'frontend_label' => 'website_id',
                'sort_order' => 1,
            ]);
        } catch (LocalizedException $e) {

        }
    }

    public function addAttribute($attributeCode, $data = array())
    {
        $this->eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, $attributeCode, [
            'type' => $data['backend_type'],
            'label' => $data['frontend_label'],
            'input' => $data['frontend_input'],
            'class' => '',
            'backend' => $data['backend_model'],
            'source' => $data['source_model'],
            'global' => '',
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => 0,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'used_in_product_listing' => true,
            'unique' => false,
            'system' => false,
            'sort_order' => $data['sort_order'],
            'apply_to' => '',
            'note' => $data['note'],
        ]);
    }
}