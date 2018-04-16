<?php

/**
 * Product:       Xtento_OrderExport (2.2.8)
 * ID:            pla2jYgPEb1bu8ncBMlGtw6aKM5PQ018LXPJPkLn5XM=
 * Packaged:      2017-06-01T10:42:36+00:00
 * Last Modified: 2016-05-29T13:37:09+00:00
 * File:          app/code/Xtento/OrderExport/Controller/Adminhtml/Destination/Save.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\OrderExport\Controller\Adminhtml\Destination;

class Save extends \Xtento\OrderExport\Controller\Adminhtml\Destination
{
    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $encryptor;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Xtento\OrderExport\Helper\Module $moduleHelper
     * @param \Xtento\XtCore\Helper\Cron $cronHelper
     * @param \Xtento\OrderExport\Model\ResourceModel\Profile\CollectionFactory $profileCollectionFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Xtento\OrderExport\Model\DestinationFactory $destinationFactory
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Xtento\OrderExport\Helper\Module $moduleHelper,
        \Xtento\XtCore\Helper\Cron $cronHelper,
        \Xtento\OrderExport\Model\ResourceModel\Profile\CollectionFactory $profileCollectionFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Xtento\OrderExport\Model\DestinationFactory $destinationFactory,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor
    ) {
        parent::__construct($context, $moduleHelper, $cronHelper, $profileCollectionFactory, $registry, $escaper, $scopeConfig, $destinationFactory);
        $this->encryptor = $encryptor;
    }

    /**
     * Save destination
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);

        /** @var $postData \Zend\Stdlib\Parameters */
        if ($postData = $this->getRequest()->getPost()) {
            $postData = $postData->toArray();
            foreach ($postData as $key => $value) {
                unset($postData[$key]);
                $postData[str_replace('dest_', '', $key)] = $value;
            }
            $model = $this->destinationFactory->create();
            #var_dump($postData); die();
            $model->setData($postData);
            $model->setLastModification(time());

            if (!$model->getId()) {
                $model->setEnabled(1);
            }

            // Handle certain fields
            if ($model->getId()) {
                $model->setPath(trim(rtrim($model->getPath(), '/')) . '/');
                if ($model->getNewPassword() !== '' && $model->getNewPassword() !== '******') {
                    $model->setPassword($this->encryptor->encrypt($model->getNewPassword()));
                }
            }

            try {
                $model->save();
                $this->_session->setFormData(false);
                $this->registry->register('orderexport_destination', $model, true);
                if (isset($postData['destination_id']) && !$this->getRequest()->getParam('switch', false)) {
                    $this->testConnection();
                }
                $this->messageManager->addSuccessMessage(__('The export destination has been saved.'));

                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath(
                        '*/*/edit',
                        ['id' => $model->getId(), 'active_tab' => $this->getRequest()->getParam('active_tab')]
                    );
                    return $resultRedirect;
                } else {
                    $resultRedirect->setPath('*/*');
                    return $resultRedirect;
                }
            } catch (\Exception $e) {
                $message = $e->getMessage();
                if (preg_match('/Notice: Undefined offset: /', $e->getMessage()) && preg_match(
                        '/SSH2/',
                        $e->getMessage()
                    )
                ) {
                    $message = 'This doesn\'t seem to be a SFTP server.';
                }
                $this->messageManager->addErrorMessage(
                    __('An error occurred while saving this export destination: %1', $message)
                );
            }

            $this->_session->setFormData($postData);
            $resultRedirect->setRefererOrBaseUrl();
            return $resultRedirect;
        } else {
            $this->messageManager->addErrorMessage(
                __('Could not find any data to save in the POST request. POST request too long maybe?')
            );
            $resultRedirect->setPath('*/*');
            return $resultRedirect;
        }
    }

    protected function testConnection()
    {
        $destination = $this->registry->registry('orderexport_destination');
        $testResult = $this->_objectManager->create(
            '\Xtento\OrderExport\Model\Destination\\' . ucfirst($destination->getType())
        )->setDestination($destination)->testConnection();
        if (!$testResult->getSuccess()) {
            $this->messageManager->addWarningMessage($testResult->getMessage());
        } else {
            $this->messageManager->addSuccessMessage($testResult->getMessage());
        }
    }
}