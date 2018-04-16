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
class Payment extends \Magento\Framework\View\Element\Html\Select
{
    protected $_paymentHelper;
    protected $_logger;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Payment\Helper\Data $paymentHelper
	)
	{
		$this->_paymentHelper = $paymentHelper;
		$this->_logger        = $context->getLogger();

//        $this->_logger->debug("paymentHelper : "+ var_dump($this->_paymentHelper->getPaymentMethodList()));

		parent::__construct($context);
	}

    public function toOptionArray()
    {
        return $this->_paymentHelper->getPaymentMethodList();
    }

    public function _toHtml()
    {
        $options = $this->toOptionArray();

        foreach ($options as $key => $value) {
            if($key && $value){
                $this->addOption($key, $value .' <'.$key.'>');
            }
        }

//        foreach ($options as $option) {
//            $this->addOption($option['value'], $option['label']);
//        }

        return parent::_toHtml();
    }

    public function setInputName($value)
    {
        return $this->setName($value);
    }
}