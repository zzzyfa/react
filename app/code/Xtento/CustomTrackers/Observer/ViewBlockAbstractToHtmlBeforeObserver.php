<?php

/**
 * Product:       Xtento_CustomTrackers (2.1.0)
 * ID:            pla2jYgPEb1bu8ncBMlGtw6aKM5PQ018LXPJPkLn5XM=
 * Packaged:      2017-06-01T10:42:53+00:00
 * Last Modified: 2016-11-15T14:02:26+00:00
 * File:          app/code/Xtento/CustomTrackers/Observer/ViewBlockAbstractToHtmlBeforeObserver.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\CustomTrackers\Observer;

use Magento\Framework\Event\ObserverInterface;

class ViewBlockAbstractToHtmlBeforeObserver implements ObserverInterface
{
    /**
     * @var \Xtento\CustomTrackers\Helper\Module
     */
    protected $moduleHelper;

    /**
     * ViewBlockAbstractToHtmlBeforeObserver constructor.
     *
     * @param \Xtento\CustomTrackers\Helper\Module $moduleHelper
     */
    public function __construct(
        \Xtento\CustomTrackers\Helper\Module $moduleHelper
    ) {
        $this->moduleHelper = $moduleHelper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($observer->getBlock() && $observer->getBlock()->getTemplate() == 'Magento_Sales::email/shipment/track.phtml') {
            if (!$this->moduleHelper->isModuleEnabled()) {
                return $this;
            }
            $observer->getBlock()->setTemplate('Xtento_CustomTrackers::email/shipment/track.phtml');
        }
        return $this;
    }
}
