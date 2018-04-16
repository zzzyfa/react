<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Block\Adminhtml\Request\Edit\Tab;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Store\Model\ScopeInterface;

class Request extends Generic implements TabInterface
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
     * @var \Magento\Framework\Url
     */
    protected $frontendUrl;

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
        \Magento\Framework\Url $frontendUrl,
        array $data = []
    ) {
        $this->frontendUrl = $frontendUrl;
        $this->yesNo = $yesNo;
        $this->templateSource = $templateSource;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Request');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Request');
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
        /** @var \Amasty\Rma\Model\Request $model */
        $model = $this->_coreRegistry->registry('amrma_request');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('request_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General Information')]
        );

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }

        $fieldset->addField('request_id', 'label', [
            'label'     => __('ID'),
            'value'     => $model->getId(),
        ]);

        $fieldset->addField('increment_id', 'link', [
            'label'     => __('Order #'),
            'href' => $this->getUrl('sales/order/view', [
                'order_id' => $model->getData('order_id')
            ]),
            'name'      => 'increment_id',
        ]);

        $fieldset->addField('email', 'label', [
            'label'     => __('Email'),
            'name'      => 'email',
        ]);

        if ($model->getData('customer_id')) {
            $fieldset->addField('link', 'link', [
                'label'     => __('Customer'),
                'href'      => $this->getUrl(
                    'customer/index/edit', ['id' => $model->getData('customer_id')]
                ),
                'value' => $model->getCustomerName()
            ]);
        }
        else {
            $fieldset->addField('link', 'label', [
                'label' => __('Customer'),
                'value' => __('Guest')
            ]);
        }

        if ($model->isStatusAllowPrintLabel()) {
            $fieldset->addField('code', 'label', [
                'label' => __('Code'),
                'value' => $model->getData('code')
            ]);

            $fieldset->addField('is_shipped', 'label', [
                'label' => __('Is Shipped'),
            ]);

            $fieldset->addField('shipping_label', 'link', [
                'label'     => '',
                'href' => $this->frontendUrl->getUrl(
                    'amasty_rma/request/export',
                    [
                        '_nosid' => true,
                        'code' => $model->getData('code'),
                        'id' => $model->getId()
                    ]
                ),
                'onclick' => 'window.open(this.href, \''. __('Printing') .'\', \'menubar=yes,location=yes,resizable=no,scrollbars=no,status=yes,width=500,height=500\')    ; return false;',
                'value' => __("View Shipping Label")
            ]);
        }


        if ($extraFields = $model->getExtraFields()) {
            $fldInfo = $form->addFieldset(
                'extra',
                ['legend' => $this->_scopeConfig->getValue(
                    'amrma/extra/title',
                    ScopeInterface::SCOPE_STORE
                )]
            );

            foreach ($extraFields as $index => $fieldInfo) {
                $fldInfo->addField(
                    'field_' . $index, 'label', [
                    'label' => $fieldInfo['label'],
                    'name'  => 'field_' . $index,
                ]);
            }
        }

        $form->addValues($model->getData());
        $form->addValues([
            'is_shipped' => $model->getData('is_shipped') ? __('Yes') : __('No')
        ]);

        $this->setForm($form);
        return parent::_prepareForm();
    }

    protected function _toHtml()
    {
        $result = parent::_toHtml();

        $result .= $this->_layout
            ->createBlock('\Amasty\Rma\Block\Adminhtml\Request\Edit\Comments')
            ->toHtml();

        return $result;
    }
}
