<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Controller\Adminhtml\Status;

use Amasty\Rma\Controller\Adminhtml\Status as StatusAction;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Cms\Model\ResourceModel\Block\CollectionFactory;

class MassAction extends StatusAction
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @param Context $context
     * @param Filter $filter
     */
    public function __construct(Context $context, Filter $filter)
    {
        $this->filter = $filter;

        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $action = $this->getRequest()->getParam('action');

        $rawCollection = $this->_objectManager->create('\Amasty\Rma\Model\ResourceModel\Status\Collection');

        $collection = $this->filter->getCollection($rawCollection);
        $collectionSize = $collection->getSize();

        if (in_array($action, ['delete', 'activate', 'deactivate'])) {
            $collection->walk($action);

            switch($action) {
                case 'delete':
                    $message = __('A total of %1 record(s) have been deleted.', $collectionSize);
                    break;
                case 'deactivate':
                    $message = __('A total of %1 record(s) have been deactivated.', $collectionSize);
                    break;
                default:
                    $message = __('A total of %1 record(s) have been activated.', $collectionSize);
                    break;
            }
            $this->messageManager->addSuccessMessage($message);
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
