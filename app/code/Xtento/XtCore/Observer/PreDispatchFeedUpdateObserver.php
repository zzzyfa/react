<?php

/**
 * Product:       Xtento_XtCore (2.0.7)
 * ID:            pla2jYgPEb1bu8ncBMlGtw6aKM5PQ018LXPJPkLn5XM=
 * Packaged:      2017-06-01T10:43:07+00:00
 * Last Modified: 2015-11-26T12:22:34+00:00
 * File:          app/code/Xtento/XtCore/Observer/PreDispatchFeedUpdateObserver.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\XtCore\Observer;

class PreDispatchFeedUpdateObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Xtento\XtCore\Model\FeedFactory
     */
    protected $feedFactory;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $backendAuthSession;

    /**
     * @param \Xtento\XtCore\Model\FeedFactory $feedFactory
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     */
    public function __construct(
        \Xtento\XtCore\Model\FeedFactory $feedFactory,
        \Magento\Backend\Model\Auth\Session $backendAuthSession
    ) {
        $this->feedFactory = $feedFactory;
        $this->backendAuthSession = $backendAuthSession;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->backendAuthSession->isLoggedIn()) {
            $feedModel = $this->feedFactory->create();
            /* @var $feedModel \Xtento\XtCore\Model\Feed */
            $feedModel->checkUpdate();
        }
    }
}
