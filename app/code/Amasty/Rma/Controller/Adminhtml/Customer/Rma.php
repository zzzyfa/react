<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Controller\Adminhtml\Customer;

use Magento\Backend\App\Action;

class Rma extends Action
{
    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @param \Magento\Backend\App\Action\Context          $context
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Framework\Registry                  $registry
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);

        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->_coreRegistry = $registry;
    }

    public function execute()
    {
        $customerId = (int)$this->_request->getParam('id');

        $result = $this->resultLayoutFactory->create();
        $result->getLayout()->getBlock('amasty_rma')->setData('customer_id', $customerId);

        return $result;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_Rma::requests');
    }
}
