<?php
/**
* BSS Commerce Co.
*
* NOTICE OF LICENSE
*
* This source file is subject to the EULA
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://bsscommerce.com/Bss-Commerce-License.txt
*
* =================================================================
*                 MAGENTO EDITION USAGE NOTICE
* =================================================================
* This package designed for Magento COMMUNITY edition
* BSS Commerce does not guarantee correct work of this extension
* on any other Magento edition except Magento COMMUNITY edition.
* BSS Commerce does not provide extension support in case of
* incorrect edition usage.
* =================================================================
*
* @category   BSS
* @package    Bss_MinMaxQtyOrderPerCate
* @author     Extension Team
* @copyright  Copyright (c) 2014-2016 BSS Commerce Co. ( http://bsscommerce.com )
* @license    http://bsscommerce.com/Bss-Commerce-License.txt
*/

namespace Bss\MinMaxQtyOrderPerCate\Block\Adminhtml\System\Config\Form;
class Minmaxqty extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray {
    /**
     * Grid columns
     *
     * @var array
     */
    protected $_columns = [];
    protected $_customerGroupRenderer;
    protected $_categoryRenderer;

    protected $_addAfter = true;

    protected $_addButtonLabel;

    protected function _construct() {
        parent::_construct();
        $this->_addButtonLabel = __('Add');
    }

    protected function  _getCategoryRenderer() 
    {
        if (!$this->_categoryRenderer) {
            $this->_categoryRenderer = $this->getLayout()->createBlock(
                'Bss\MinMaxQtyOrderPerCate\Block\Adminhtml\System\Config\Form\Category', '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->_categoryRenderer->setClass('category_select validate-select');
            $this->_categoryRenderer->setExtraParams('style="width:150px"');
        }
        return $this->_categoryRenderer;
    } 
    protected function _getCustomerGroupRenderer() {
        if (!$this->_customerGroupRenderer) {
            $this->_customerGroupRenderer = $this->getLayout()->createBlock(
                    'Bss\MinMaxQtyOrderPerCate\Block\Adminhtml\System\Config\Form\Customergroup', '', ['data' => ['is_render_to_js_template' => true]]
            );

            $this->_customerGroupRenderer->setClass('customer_group_select validate-select');
        }
        return $this->_customerGroupRenderer;
    }
    /**
     * Prepare to render
     *
     * @return void
     */
    protected function _prepareToRender() {
        $this->addColumn(
                'customer_group_id', [
            'label' => __('Customer Group'),
            'renderer' => $this->_getCustomerGroupRenderer(),
                ]
        );
        $this->addColumn(
                'category_id', [
            'label' => __('Category'),
            'renderer' => $this->_getCategoryRenderer(),
                ]
        );
        $this->addColumn('min_sale_qty', array('label' => __('Min Qty')));
        $this->addColumn('max_sale_qty', array('label' => __('Max Qty')));
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Qty');
    }
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row) {
        $optionExtraAttr = [];
        $optionExtraAttr['option_' . $this->_getCustomerGroupRenderer()->calcOptionHash($row->getData('customer_group_id'))] =
            'selected="selected"';
        $optionExtraAttr['option_' . $this->_getCategoryRenderer()->calcOptionHash($row->getData('category_id'))] =
            'selected="selected"';
        $row->setData(
            'option_extra_attrs',
            $optionExtraAttr
        );

        }
    /**
     * Render array cell for prototypeJS template
     *
     * @param string $columnName
     * @return string
     * @throws \Exception
     */
    public function renderCellTemplate($columnName)
    {
        if ($columnName == "min_sale_qty" || $columnName == "max_sale_qty") {
            $this->_columns[$columnName]['class'] = 'input-text validate-number validate-greater-than-zero';
            $this->_columns[$columnName]['style'] = 'width:50px';
        }
        return parent::renderCellTemplate($columnName);
    }
}