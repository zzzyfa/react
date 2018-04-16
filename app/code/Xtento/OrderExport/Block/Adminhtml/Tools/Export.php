<?php

/**
 * Product:       Xtento_OrderExport (2.2.8)
 * ID:            pla2jYgPEb1bu8ncBMlGtw6aKM5PQ018LXPJPkLn5XM=
 * Packaged:      2017-06-01T10:42:36+00:00
 * Last Modified: 2015-10-11T13:28:37+00:00
 * File:          app/code/Xtento/OrderExport/Block/Adminhtml/Tools/Export.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\OrderExport\Block\Adminhtml\Tools;

class Export extends \Magento\Backend\Block\Template
{
    /**
     * @var \Xtento\OrderExport\Model\ResourceModel\Destination\CollectionFactory
     */
    protected $destinationCollectionFactory;

    /**
     * @var \Xtento\OrderExport\Model\ResourceModel\Profile\CollectionFactory
     */
    protected $profileCollectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Xtento\OrderExport\Model\ResourceModel\Destination\CollectionFactory $destinationCollectionFactory
     * @param \Xtento\OrderExport\Model\ResourceModel\Profile\CollectionFactory $profileCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Xtento\OrderExport\Model\ResourceModel\Destination\CollectionFactory $destinationCollectionFactory,
        \Xtento\OrderExport\Model\ResourceModel\Profile\CollectionFactory $profileCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->destinationCollectionFactory = $destinationCollectionFactory;
        $this->profileCollectionFactory = $profileCollectionFactory;
    }

    public function getProfiles()
    {
        $profileCollection = $this->profileCollectionFactory->create();
        $profileCollection->getSelect()->order('name ASC');
        return $profileCollection;
    }

    public function getDestinations()
    {
        $destinationCollection = $this->destinationCollectionFactory->create();
        $destinationCollection->getSelect()->order('name ASC');
        return $destinationCollection;
    }
}
