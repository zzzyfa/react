<?php

namespace Althea\Customer\Setup;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface {

    private $_eavAttribute;
    private $_customerSetupFactory;
    private $_attributeSetCollection;
    /* @var Config $_eavConfig */
    private $_eavConfig;
    private $_moduleDataSetup;
    private $_customerSetup;

    public function __construct(
        \Magento\Eav\Model\ConfigFactory $eavAttributeFactory,
        CustomerSetupFactory $customerSetupFactory
    )
    {
        $this->_eavAttribute         = $eavAttributeFactory;
        $this->_customerSetupFactory = $customerSetupFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1') < 0) {

            $this->addSurveyCustomerAttribute($setup);
        }

        if (version_compare($context->getVersion(), '1.0.2') < 0) {

            $this->setCustomerAddressRuleValidation($setup);
        }

        $setup->endSetup();
    }

    public function addSurveyCustomerAttribute(ModuleDataSetupInterface $setup)
    {
        /* @var CustomerSetup $customerSetup */
        $customerSetup = $this->_customerSetupFactory->create(['setup' => $setup]);

        $this->_customerSetup = $customerSetup;

        // remove previously set attribute
        $customerSetup->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, 'customer_register_additional');
        $customerSetup->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, 'customer_attribute_age');
        $customerSetup->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, 'customer_attribute_skintype');
        $customerSetup->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, 'customer_attribute_skintone');
        $customerSetup->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, 'customer_attribute_skintype_label');
        $customerSetup->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, 'customer_attribute_skinconcern_label');
        $customerSetup->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, 'customer_attribute_skintone_label');

        try {

            $this->_eavConfig              = $customerSetup->getEavConfig();
            $customerEntity                = $this->_eavConfig->getEntityType(Customer::ENTITY);
            $this->_attributeSetCollection = $customerEntity->getAttributeSetCollection()
                ->setOrder('attribute_set_id', 'ASC');

            $this->addCustomerAttribute($customerSetup, 'customer_attribute_age', [
                'backend_type'   => 'text',
                'frontend_input' => 'select',
                'note' => 'How old are you?',
                'source_model'   => 'Althea\Customer\Model\Eav\Entity\Attribute\Source\Age',
                'backend_model'  => '',
                'frontend_label' => 'Age:',
                'sort_order'     => 1,
            ]);

            $this->addCustomerAttribute($customerSetup, 'customer_attribute_skintype', [
                'backend_type'   => 'text',
                'frontend_input' => 'select',
                'note' => 'What is your skin type?',
                'source_model'   => 'Althea\Customer\Model\Eav\Entity\Attribute\Source\Skintype',
                'backend_model'  => '',
                'frontend_label' => 'Skin Type:',
                'sort_order'     => 5,
            ]);

            $this->addCustomerAttribute($customerSetup, 'customer_attribute_skinconcern', [
                'backend_type'   => 'text',
                'frontend_input' => 'multiselect',
                'note' => 'What are your main skin concerns (check all that apply)?',
                'source_model'   => 'Althea\Customer\Model\Eav\Entity\Attribute\Source\Skinconcern',
                'backend_model'  => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'frontend_label' => 'Skin Concern:',
                'sort_order'     => 9,
            ]);

            $this->addCustomerAttribute($customerSetup, 'customer_attribute_skintone', [
                'backend_type'   => 'text',
                'frontend_input' => 'select',
                'note' => 'What is your skin tone?',
                'source_model'   => 'Althea\Customer\Model\Eav\Entity\Attribute\Source\Skintone',
                'backend_model'  => '',
                'frontend_label' => 'Skin Tone:',
                'sort_order'     => 13,
            ]);
        } catch (LocalizedException $e) {

        }
    }

    public function addCustomerAttribute(CustomerSetup $customerSetup, $attributeCode, $data = array())
    {
        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, $attributeCode, [
            'type'                    => $data['backend_type'],
            'label'                   => $data['frontend_label'],
            'input'                   => $data['frontend_input'],
            'class'                   => '',
            'backend'                 => $data['backend_model'],
            'source'                  => $data['source_model'],
            'global'                  => '',
            'visible'                 => true,
            'required'                => false,
            'user_defined'            => true,
            'default'                 => 0,
            'searchable'              => false,
            'filterable'              => false,
            'comparable'              => false,
            'visible_on_front'        => false,
            'used_in_product_listing' => true,
            'unique'                  => false,
            'system'                  => false,
            'sort_order'              => $data['sort_order'],
            'apply_to'                => '',
            'note'                    => $data['note'],
        ]);

        try {

            $attribute = $this->_eavConfig->getAttribute(Customer::ENTITY, $attributeCode);

            /* @var Set $attributeSet */
            foreach ($this->_attributeSetCollection as $attributeSet) {

                $attribute->addData([
                    'attribute_set_id'   => $attributeSet->getId(),
                    'attribute_group_id' => ($id = $attributeSet->getDefaultGroupId()) ? $id : '1',
                    'sort_order'         => $data['sort_order'],
                    'used_in_forms'      => [
                        'customer_account_create',
                        'customer_account_edit',
                    ],
                ]);

                $attribute->save();
            }
        } catch (LocalizedException $e) {
            echo $e->getMessage() . "\n";
        } catch (\Exception $e) {
            echo $e->getMessage() . "\n";
        }
    }

    public function setCustomerAddressRuleValidation(ModuleDataSetupInterface $setup)
    {
        /* @var CustomerSetup $customerSetup */
        $customerSetup = $this->_customerSetupFactory->create(['setup' => $setup]);

        $this->_customerSetup = $customerSetup;
        $this->_eavConfig              = $customerSetup->getEavConfig();
        $telephone_AttributeId =
            $this->_eavConfig
                ->getAttribute('customer_address', 'telephone')
                ->getAttributeId();

        $setup->getConnection()->update($setup->getTable('customer_eav_attribute'),
            ['validate_rules' => 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:1;}'],
            ['attribute_id IN (?)' => $telephone_AttributeId]);
    }
}