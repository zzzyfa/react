<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Althea\AutoCancelOrder\Block\Index;
use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Framework\Registry;
/**
 * Invoice view  comments form
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Index extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'index/index.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $_autoCancelOrder;

    /**
     * @param TemplateContext $context
     * @param Registry $registry
     * @param PaymentHelper $paymentHelper
     * @param AddressRenderer $addressRenderer
     * @param array $data
     */
    public function __construct(
        TemplateContext $context,
        Registry $registry,
        \Althea\AutoCancelOrder\Model\Cancel $cancel,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->_autoCancelOrder = $cancel;

        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _prepareLayout()
    {
//        $this->_autoCancelOrder->processCancel();
//        $this->_autoCancelOrder->registerCancel();
    }
}
