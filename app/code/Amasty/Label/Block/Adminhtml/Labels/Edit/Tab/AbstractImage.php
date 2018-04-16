<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */

// @codingStandardsIgnoreFile

namespace Amasty\Label\Block\Adminhtml\Labels\Edit\Tab;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Convert\DataObject as ObjectConverter;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\SalesRule\Model\RuleFactory;
use Magento\Store\Model\System\Store;

/**
 * Cart Price Rule General Information Tab
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class AbstractImage extends Generic implements TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magento\Framework\Convert\DataObject
     */
    protected $_objectConverter;

    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    protected $_salesRule;

    /**
     * @var GroupRepositoryInterface
     */
    protected $groupRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $_searchCriteriaBuilder;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param RuleFactory $salesRule
     * @param ObjectConverter $objectConverter
     * @param Store $systemStore
     * @param GroupRepositoryInterface $groupRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        RuleFactory $salesRule,
        ObjectConverter $objectConverter,
        Store $systemStore,
        GroupRepositoryInterface $groupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        \Amasty\Label\Helper\Data $helper,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_objectConverter = $objectConverter;
        $this->_salesRule = $salesRule;
        $this->groupRepository = $groupRepository;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_helper = $helper;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return '';
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
        return parent::_prepareForm();
    }

    protected function getImageHtml($field, $img)
    {
        $html = '';
        if ($img) {
            $html .= '<p style="margin-top: 5px">';
            $html .= '<img style="max-width:300px" src="' . $this->_helper->getImageUrl($img) . '" />';
            $html .= '<br/><input type="checkbox" value="1" name="remove_' . $field . '"/> ' . __('Remove');
            $html .= '<input type="hidden" value="' . $img . '" name="old_' . $field . '"/>';
            $html .= '</p>';
        }
        return $html;
    }

    protected function _getTextNote(){
        // {ATTR:code} - attribute value, {STOCK_QTY} - quantity in stock
        return 'Variables: {PRICE} - regular price; {BR} - new line;<br/>
                    {SAVE_PERCENT} - save percent;
                    {SAVE_AMOUNT} - save amount;<br/>
                    {SPECIAL_PRICE} - special price;
                    {ATTR:code} - attribute value, e.g. {ATTR:color};<br/>
                    {SPDL} - days left for special price;
                    {SPHL} - hours left for special price;<br/>
                    {NEW_FOR} - days ago the product was added;
                    {SKU} - product SKU; {STOCK} - product qty.';
    }

    protected function getPositionHtml($field)
    {
        $html = '<table id="amlabel-table-' . $field . '" class="amlabel-table-position">
            <tr><td></td><td></td><td></td></tr>
            <tr><td></td><td></td><td></td></tr>
            <tr><td></td><td></td><td></td></tr>
            </table>';
        $html .= '<script>
            require([
              "jquery",
              "Amasty_Label/js/amlabel"
            ], function ($) {
               $("#labels_' . $field . '").amLabelPosition();
            });
        </script>';

        return $html;
    }

    protected function _restoreSizeColor($data){
        if(array_key_exists('prod_style', $data) && $data['prod_style']) {
            $prodStyles = $data['prod_style'];

            $template = '@font-size: (.*?);@s';
            preg_match_all($template, $prodStyles, $res);
            if (isset($res[1]) && isset($res[1][0])) {
                $data['prod_size'] = $res[1][0];
            }

            $template = '@color: (.*?);@s';
            preg_match_all($template, $prodStyles, $res);
            if (isset($res[1]) && isset($res[1][0])) {
                $data['prod_color'] = $res[1][0];
            }
        }

        if(array_key_exists('cat_style', $data) && $data['cat_style']) {
            $catStyles = $data['cat_style'];

            $template = '@font-size: (.*?);@s';
            preg_match_all($template, $catStyles, $res);
            if (isset($res[1]) && isset($res[1][0])) {
                $data['cat_size'] = $res[1][0];
            }

            $template = '@color: (.*?);@s';
            preg_match_all($template, $catStyles, $res);
            if (isset($res[1]) && isset($res[1][0])) {
                $data['cat_color'] = $res[1][0];
            }
        }

        return $data;
    }
}
