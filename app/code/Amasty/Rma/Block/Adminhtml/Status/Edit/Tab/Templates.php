<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Block\Adminhtml\Status\Edit\Tab;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class Templates extends Generic implements TabInterface
{
    /**
     * @var \Amasty\Rma\Model\Source\EmailTemplate
     */
    protected $templateSource;

    /**
     * General constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param \Amasty\Rma\Model\Source\EmailTemplate  $templateSource
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Amasty\Rma\Model\Source\EmailTemplate $templateSource,
        array $data = []
    ) {
        $this->templateSource = $templateSource;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Templates');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Templates');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $options = $this->templateSource->asArray();

        $status = $this->_coreRegistry->registry('amrma_status');
        $templates = $status->getStoreTemplates();

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('status_');

        $fieldset = $form->addFieldset(
            'store_templates_fieldset',
            ['legend' => __('Store View Specific Templates')]
        );

        /** @var \Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset $renderer */
        $renderer = $this->getLayout()->createBlock('\Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset');
        $fieldset->setRenderer($renderer);

        /** @var \Magento\Store\Model\Website $website */
        foreach ($this->_storeManager->getWebsites() as $website) {
            $fieldset->addField("w_{$website->getId()}_label", 'note', array(
                'label'    => $website->getName(),
                'fieldset_html_class' => 'website',
            ));
            /** @var \Magento\Store\Model\Group $group */
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                if (count($stores) == 0) {
                    continue;
                }
                $fieldset->addField("sg_{$group->getId()}_label", 'note', array(
                    'label'    => $group->getName(),
                    'fieldset_html_class' => 'store-group',
                ));
                /** @var \Magento\Store\Model\Store $store */
                foreach ($stores as $store) {
                    $fieldset->addField("s_{$store->getId()}", 'select', array(
                        'name'      => 'store_templates['.$store->getId().']',
                        'required'  => false,
                        'label'     => $store->getName(),
                        'value'     => isset($templates[$store->getId()]) ? $templates[$store->getId()] : '',
                        'fieldset_html_class' => 'store',
                        'options'   => $options,
                    ));
                }
            }
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
