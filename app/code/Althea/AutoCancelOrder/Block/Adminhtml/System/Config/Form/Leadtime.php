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

namespace Althea\AutoCancelOrder\Block\Adminhtml\System\Config\Form;
class Leadtime extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * Grid columns
     *
     * @var array
     */
    protected $_columns = [];
    protected $_paymentRenderer;

    protected $_addAfter = true;

    protected $_addButtonLabel;

    protected function _construct()
    {
        parent::_construct();
        $this->_addButtonLabel = __('Add');
    }

    protected function  _getPaymentRenderer()
    {
        if (!$this->_paymentRenderer) {
            $this->_paymentRenderer = $this->getLayout()->createBlock(
                'Althea\AutoCancelOrder\Block\Adminhtml\System\Config\Form\Payment', '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->_paymentRenderer->setClass('payment_select validate-select');
            $this->_paymentRenderer->setExtraParams('style="width:150px"');
        }
        return $this->_paymentRenderer;
    }

    /**
     * Prepare to render
     *
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'payment_id', [
                'label' => __('Payment'),
                'renderer' => $this->_getPaymentRenderer(),
            ]
        );
        $this->addColumn('schedule', array('label' => __('Cron Schedule (Min)')));
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Payment');
    }

    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $optionExtraAttr = [];

        $optionExtraAttr['option_' . $this->_getPaymentRenderer()->calcOptionHash($row->getData('payment_id'))] =
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
        if ($columnName == "schedule") {
            $this->_columns[$columnName]['class'] = 'input-text validate-number validate-greater-than-zero';
            $this->_columns[$columnName]['style'] = 'width:50px';
        }
        return parent::renderCellTemplate($columnName);
    }
}