<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Controller\Request;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Action\Context;

class Export extends \Amasty\Rma\Controller\Request
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * View constructor.
     *
     * @param Context                                    $context
     * @param \Magento\Customer\Model\Session            $customerSession
     * @param \Amasty\Rma\Model\Session                  $rmaSession
     * @param \Magento\Framework\Registry                $registry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param ScopeConfigInterface                       $scopeConfig
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Amasty\Rma\Model\Session $rmaSession,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,

        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context, $customerSession, $rmaSession, $registry);

        $this->resultPageFactory = $resultPageFactory;
        $this->scopeConfig = $scopeConfig;
    }
    
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $code = $this->getRequest()->getParam('code');

        if ($code) {
            /** @var \Amasty\Rma\Model\Request $request */
            $request = $this->_objectManager->create('\Amasty\Rma\Model\Request');
            $request->load($code, 'code');

            $this->registry->register('amrma_request', $request);
        } else {
            $request = $this->_initRequest($id);

            if (!$request)
                return $this->goHome();

            if (!$request->isStatusAllowPrintLabel()) {
                return $this->_redirect('*/*/view', ['id' => $id]);
            }

            $allowPrintLabel = $this->scopeConfig->isSetFlag(
                'amrma/general/print_label', ScopeInterface::SCOPE_STORE
            );

            if (!$allowPrintLabel || !$request->isStatusAllowPrintLabel()) {
                throw new LocalizedException(__('Access denied.'));
            }
        }
        
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        return $resultPage;
    }
}
