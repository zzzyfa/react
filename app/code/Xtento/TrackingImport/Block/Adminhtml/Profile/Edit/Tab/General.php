<?php

/**
 * Product:       Xtento_TrackingImport (2.1.9)
 * ID:            pla2jYgPEb1bu8ncBMlGtw6aKM5PQ018LXPJPkLn5XM=
 * Packaged:      2017-06-01T10:43:01+00:00
 * Last Modified: 2016-04-11T13:30:18+00:00
 * File:          app/code/Xtento/TrackingImport/Block/Adminhtml/Profile/Edit/Tab/General.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Block\Adminhtml\Profile\Edit\Tab;

class General extends \Xtento\TrackingImport\Block\Adminhtml\Widget\Tab implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $yesNo;

    /**
     * @var \Xtento\TrackingImport\Model\System\Config\Source\Import\Entity
     */
    protected $importEntity;

    /**
     * @var \Xtento\TrackingImport\Model\System\Config\Source\Import\Processor
     */
    protected $importProcessor;

    /**
     * General constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Config\Model\Config\Source\Yesno $yesNo
     * @param \Xtento\TrackingImport\Model\System\Config\Source\Import\Entity $importEntity
     * @param \Xtento\TrackingImport\Model\System\Config\Source\Import\Processor $importProcessor
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Config\Model\Config\Source\Yesno $yesNo,
        \Xtento\TrackingImport\Model\System\Config\Source\Import\Entity $importEntity,
        \Xtento\TrackingImport\Model\System\Config\Source\Import\Processor $importProcessor,
        array $data = []
    ) {
        $this->yesNo = $yesNo;
        $this->importEntity = $importEntity;
        $this->importProcessor = $importProcessor;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function getFormMessages()
    {
        $formMessages = [];
        $model = $this->_coreRegistry->registry('trackingimport_profile');
        if ($model->getId() && !$model->getEnabled()) {
            $formMessages[] = [
                'type' => 'warning',
                'message' => __(
                    'This profile is disabled. No automatic imports will be made and the profile won\'t show up for manual imports.'
                )
            ];
        }
        return $formMessages;
    }

    /**
     * Prepare form
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('trackingimport_profile');
        // Set default values
        if (!$model->getId()) {
            $model->setEnabled(1);
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('General Configuration'),
            ]
        );

        if ($model->getId()) {
            $fieldset->addField(
                'profile_id',
                'hidden',
                [
                    'name' => 'profile_id',
                ]
            );
        }

        $fieldset->addField(
            'name',
            'text',
            [
                'label' => __('Name'),
                'name' => 'name',
                'required' => true,
            ]
        );

        if ($model->getId()) {
            $fieldset->addField(
                'enabled',
                'select',
                [
                    'label' => __('Enabled'),
                    'name' => 'enabled',
                    'values' => $this->yesNo->toOptionArray()
                ]
            );
        }

        $processor = $fieldset->addField(
            'processor',
            'select',
            [
                'label' => __('File Processor'),
                'name' => 'processor',
                'options' => $this->importProcessor->toOptionArray(),
                'required' => true,
                'note' => __(
                    'This setting can\'t be changed after creating the profile. Add a new profile for different import processors.'
                )
            ]
        );

        $entity = $fieldset->addField(
            'entity',
            'select',
            [
                'label' => __('Import Entity'),
                'name' => 'entity',
                'options' => $this->importEntity->toOptionArray(),
                'required' => true,
                'note' => __(
                    'This setting can\'t be changed after creating the profile. Add a new profile for different import types.'
                )
            ]
        );

        if ($model->getId()) {
            $entity->setDisabled(true);
            $processor->setDisabled(true);
        }

        if (!$this->_coreRegistry->registry('trackingimport_profile')
            || !$this->_coreRegistry->registry('trackingimport_profile')->getId()
        ) {
            $fieldset->addField(
                'continue_button',
                'note',
                [
                    'text' => $this->getChildHtml('continue_button'),
                ]
            );
        }

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            'continue_button',
            $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
                ->setData(
                    [
                        'label' => __('Continue'),
                        'data_attribute' => [
                            'mage-init' => [
                                'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                            ],
                        ],
                        'class' => 'save'
                    ]
                )
        );
        return parent::_prepareLayout();
    }

    /**
     * Prepare label for tab
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('General Configuration');
    }

    /**
     * Prepare title for tab
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('General Configuration');
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
}