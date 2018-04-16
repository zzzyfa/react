<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Controller\Adminhtml\VisitHistory;

class Index extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;

    protected $_helper;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        /**
         * @var \Magento\Backend\Model\View\Result\Page $resultPage
         * */
        $pageResult = $this->resultPageFactory->create();
        $pageResult->getLayout();
        $pageResult->setActiveMenu('Amasty_AdminActionsLog::amaudit');
        $pageResult->addBreadcrumb(__('Admin Actions Log'), __('Visit History'));
        $pageResult->addContent($pageResult->getLayout()->createBlock('Amasty\AdminActionsLog\Block\Adminhtml\VisitHistory'));
        $pageResult->getConfig()->getTitle()->prepend(__('Visit History'));

        return $pageResult;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_AdminActionsLog::page_visit_history');
    }
}
