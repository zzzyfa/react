<?php

/**
 * Product:       Xtento_TrackingImport (2.1.9)
 * ID:            pla2jYgPEb1bu8ncBMlGtw6aKM5PQ018LXPJPkLn5XM=
 * Packaged:      2017-06-01T10:43:01+00:00
 * Last Modified: 2016-05-30T12:56:10+00:00
 * File:          app/code/Xtento/TrackingImport/Controller/Adminhtml/Source/Save.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Controller\Adminhtml\Source;

class Save extends \Xtento\TrackingImport\Controller\Adminhtml\Source
{
    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $encryptor;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Xtento\TrackingImport\Helper\Module $moduleHelper
     * @param \Xtento\XtCore\Helper\Cron $cronHelper
     * @param \Xtento\TrackingImport\Model\ResourceModel\Profile\CollectionFactory $profileCollectionFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Xtento\TrackingImport\Model\SourceFactory $sourceFactory
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Xtento\TrackingImport\Helper\Module $moduleHelper,
        \Xtento\XtCore\Helper\Cron $cronHelper,
        \Xtento\TrackingImport\Model\ResourceModel\Profile\CollectionFactory $profileCollectionFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Xtento\TrackingImport\Model\SourceFactory $sourceFactory,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor
    ) {
        parent::__construct(
            $context,
            $moduleHelper,
            $cronHelper,
            $profileCollectionFactory,
            $registry,
            $escaper,
            $scopeConfig,
            $sourceFactory
        );
        $this->encryptor = $encryptor;
    }

    /**
     * Save source
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
                $postData[str_replace('src_', '', $key)] = $value;
            }
            $model = $this->sourceFactory->create();
            #var_dump($postData); die();
            $model->setData($postData);
            $model->setLastModification(time());

            if (!$model->getId()) {
                $model->setEnabled(1);
            }

            // Handle certain fields
            if ($model->getId()) {
                $model->setPath(trim(rtrim($model->getPath(), '/')) . '/');
                if ($model->getArchivePath() !== '') {
                    $model->setArchivePath(trim(rtrim($model->getArchivePath(), '/')) . '/');
                }
                if ($model->getNewPassword() !== '' && $model->getNewPassword() !== '******') {
                    $model->setPassword($this->encryptor->encrypt($model->getNewPassword()));
                }
            }

            try {
                $model->save();
                $this->_session->setFormData(false);
                $this->registry->register('trackingimport_source', $model, true);
                if (isset($postData['source_id']) && !$this->getRequest()->getParam('switch', false)) {
                    $this->testConnection();
                }
                $this->messageManager->addSuccessMessage(__('The import source has been saved.'));

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
                    __('An error occurred while saving this import source: %1', $message)
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
        $source = $this->registry->registry('trackingimport_source');
        $testResult = $this->_objectManager->create(
            '\Xtento\TrackingImport\Model\Source\\' . ucfirst($source->getType())
        )->setSource($source)->testConnection();
        if (!$testResult->getSuccess()) {
            $this->messageManager->addWarningMessage($testResult->getMessage());
        } else {
            $this->messageManager->addSuccessMessage($testResult->getMessage());
        }
    }
}