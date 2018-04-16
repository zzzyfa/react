<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Block\Adminhtml\Status\Edit\Tab;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class General extends Generic implements TabInterface
{
    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $yesNo;

    /**
     * @var \Amasty\Rma\Model\Source\EmailTemplate
     */
    protected $templateSource;

    /**
     * General constructor.
     *
     * @param \Magento\Backend\Block\Template\Context   $context
     * @param \Magento\Framework\Registry               $registry
     * @param \Magento\Framework\Data\FormFactory       $formFactory
     * @param \Magento\Config\Model\Config\Source\Yesno $yesNo
     * @param array                                     $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Config\Model\Config\Source\Yesno $yesNo,
        \Amasty\Rma\Model\Source\EmailTemplate $templateSource,
        array $data = []
    ) {
        $this->yesNo = $yesNo;
        $this->templateSource = $templateSource;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('General');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('General');
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
        $model = $this->_coreRegistry->registry('amrma_status');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('status_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('General')]);
        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }

        $labels = $model->getStoreLabels();
        $template = $model->getStoreTemplates();

        $fieldset->addField('store_default_label', 'text', [
            'name'      => 'store_labels[0]',
            'required'  => true,
            'label'     => __('Label'),
            'value'     => isset($labels[0]) ? $labels[0] : '',
        ]);

        if (!$model->getData('status_key')) {
            $fieldset->addField(
                'is_active', 'select', [
                'label'   => __('Active'),
                'name'    => 'is_active',
                'options' => $this->yesNo->toArray(),
                'value'   => $model->getIsActive()
            ]);
        }

        $fieldset->addField('allow_print_label', 'select', [
            'label'     => __('Allow Print Labels'),
            'name'      => 'allow_print_label',
            'options'   => $this->yesNo->toArray(),
        ]);

        $fieldset->addField('email_template_id', 'select', [
            'label'     => __('Email Template'),
            'name'      => 'store_templates[0]',
            'options'   => $this->templateSource->asArray(),
            'value'     => isset($template[0]) ? $template[0] : '',
        ]);

        $fieldset->addField('priority', 'text', [
            'label'     => __('Priority'),
            'name'      => 'priority',
            'class'     => 'validate-digits',
            'value'     => $model->getPriority()
        ]);

        $form->addValues($model->getData());

        $this->setForm($form);
        return parent::_prepareForm();
    }
}
