<?php

/**
 * Product:       Xtento_GridActions (2.1.1)
 * ID:            pla2jYgPEb1bu8ncBMlGtw6aKM5PQ018LXPJPkLn5XM=
 * Packaged:      2017-06-01T10:43:07+00:00
 * Last Modified: 2017-03-22T19:14:48+00:00
 * File:          app/code/Xtento/GridActions/Controller/Adminhtml/Pdf/Invoices.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\GridActions\Controller\Adminhtml\Pdf;

use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\Order\Pdf\Invoice;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory;
use Xtento\GridActions\Ui\Component\MassAction\CustomFilter;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

/**
 * Handling print actions
 *
 * @package Xtento\GridActions\Controller\Adminhtml\Pdf
 */
class Invoices extends \Magento\Sales\Controller\Adminhtml\Order\Pdfinvoices
{
    /**
     * Invoices constructor.
     *
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param DateTime $dateTime
     * @param FileFactory $fileFactory
     * @param Invoice $pdfInvoice
     * @param CustomFilter $customFilter
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        DateTime $dateTime,
        FileFactory $fileFactory,
        Invoice $pdfInvoice,
        CustomFilter $customFilter
    ) {
        parent::__construct($context, $customFilter, $collectionFactory, $dateTime, $fileFactory, $pdfInvoice);
    }

    /**
     * Print invoices for selected orders
     *
     * @param AbstractCollection $collection
     * @return ResponseInterface|ResultInterface
     */
    protected function massAction(AbstractCollection $collection)
    {
        $invoicesCollection = $this->collectionFactory->create()->addFieldToFilter('entity_id', ['in' => $collection->getAllIds()]);
        if (!$invoicesCollection->getSize()) {
            $this->messageManager->addError(__('There are no printable documents related to selected orders.'));
            return $this->resultRedirectFactory->create()->setPath($this->getComponentRefererUrl());
        }
        return $this->fileFactory->create(
            sprintf('invoice%s.pdf', $this->dateTime->date('Y-m-d_H-i-s')),
            $this->pdfInvoice->getPdf($invoicesCollection->getItems())->render(),
            DirectoryList::VAR_DIR,
            'application/pdf'
        );
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Xtento_GridActions::invoice');
    }
}
