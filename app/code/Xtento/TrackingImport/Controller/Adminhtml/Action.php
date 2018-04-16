<?php

/**
 * Product:       Xtento_TrackingImport (2.1.9)
 * ID:            pla2jYgPEb1bu8ncBMlGtw6aKM5PQ018LXPJPkLn5XM=
 * Packaged:      2017-06-01T10:43:01+00:00
 * Last Modified: 2016-05-25T11:08:08+00:00
 * File:          app/code/Xtento/TrackingImport/Controller/Adminhtml/Action.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Controller\Adminhtml;

abstract class Action extends \Magento\Backend\App\Action
{
    /**
     * @var \Xtento\TrackingImport\Helper\Module
     */
    protected $moduleHelper;

    /**
     * @var \Xtento\XtCore\Helper\Cron
     */
    protected $cronHelper;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Xtento\TrackingImport\Model\ResourceModel\Profile\CollectionFactory
     */
    protected $profileCollectionFactory;

    /**
     * Action constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Xtento\TrackingImport\Helper\Module $moduleHelper
     * @param \Xtento\XtCore\Helper\Cron $cronHelper
     * @param \Xtento\TrackingImport\Model\ResourceModel\Profile\CollectionFactory $profileCollectionFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Xtento\TrackingImport\Helper\Module $moduleHelper,
        \Xtento\XtCore\Helper\Cron $cronHelper,
        \Xtento\TrackingImport\Model\ResourceModel\Profile\CollectionFactory $profileCollectionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->moduleHelper = $moduleHelper;
        $this->cronHelper = $cronHelper;
        $this->profileCollectionFactory = $profileCollectionFactory;
        $this->scopeConfig = $scopeConfig;
    }

    protected function healthCheck()
    {
        // Has the module been installed properly?
        if (!$this->moduleHelper->isModuleProperlyInstalled()) {
            if ($this->getRequest()->getActionName() !== 'installation') {
                return '*/index/installation';
            } else {
                return true;
            }
        } else {
            if ($this->getRequest()->getActionName() == 'installation') {
                return '*/profile/index';
            }
        }
        // Check module status
        if (!$this->moduleHelper->confirmEnabled(true) || !$this->moduleHelper->isModuleEnabled()) {
            if ($this->getRequest()->getActionName() !== 'disabled') {
                return '*/index/disabled';
            }
        } else {
            if ($this->getRequest()->getActionName() == 'disabled') {
                return '*/profile/index';
            }
        }
        if ($this->getRequest()->getActionName() !== 'redirect') {
            // Check if this module was made for the edition (CE/PE/EE) it's being run in
            if ($this->moduleHelper->isWrongEdition()) {
                $this->addError(
                    __(
                        'Attention: The installed extension version is not compatible with the Enterprise Edition of Magento. The compatibility of the currently installed extension version has only been confirmed with the Community Edition of Magento. Please go to <a href="https://www.xtento.com" target="_blank">www.xtento.com</a> to purchase or download the Enterprise Edition of this extension in our store if you\'ve already purchased it.'
                    )
                );
            }
            // Check cronjob status
            if (!$this->scopeConfig->isSetFlag('trackingimport/general/disable_cron_warning')) {
                $profileCollection = $this->profileCollectionFactory->create();
                $profileCollection->addFieldToFilter('enabled', 1); // Profile enabled
                $profileCollection->addFieldToFilter('cronjob_enabled', 1); // Cronjob enabled
                if ($profileCollection->getSize() > 0) {
                    if (!$this->cronHelper->isCronRunning()) {
                        if ((time() - $this->cronHelper->getInstallationDate()) > (60 * 30)) {
                            // Module was not installed within the last 30 minutes
                            if ($this->cronHelper->getLastCronExecution() == '') {
                                $this->addWarning(
                                    __(
                                        'Cronjob status: Cron doesn\'t seem to be set up at all. Cron did not execute within the last 15 minutes. Please make sure to set up the cronjob as explained <a href="http://support.xtento.com/wiki/Setting_up_the_Magento_cronjob_(Magento_2)" target="_blank">here</a> and check the cron status 15 minutes after setting up the cronjob properly again.'
                                    )
                                );
                            } else {
                                $this->addWarning(
                                    __(
                                        'Cronjob status: Cron doesn\'t seem to be set up properly. Cron did not execute within the last 15 minutes. Please make sure to set up the cronjob as explained <a href="http://support.xtento.com/wiki/Setting_up_the_Magento_cronjob_(Magento_2)" target="_blank">here</a> and check the cron status 15 minutes after setting up the cronjob properly again.'
                                    )
                                );
                            }
                        } // Cron status wasn't checked yet. Please check back in 30 minutes.
                    }
                }
            }
        }
        return true;
    }

    protected function addWarning($messageText)
    {
        return $this->addMsg('warning', $messageText);
    }

    protected function addError($messageText)
    {
        return $this->addMsg('error', $messageText);
    }

    protected function addMsg($type, $messageText)
    {
        $messages = $this->messageManager->getMessages();
        foreach ($messages->getItems() as $message) {
            if ($message->getText() == $messageText) {
                return false;
            }
        }
        return ($type === 'error') ?
            $this->messageManager->addComplexErrorMessage(
                'backendHtmlMessage',
                [
                    'html' => (string)$messageText
                ]
            ) :
            $this->messageManager->addComplexWarningMessage(
                'backendHtmlMessage',
                [
                    'html' => (string)$messageText
                ]
            );
    }

}