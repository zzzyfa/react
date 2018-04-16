<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Althea\AutoCancelOrder\Model\ResourceModel\Cancel;

/**
 * Class CollectionFactory
 */
class CollectionFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager = null;

    /**
     * Instance name to create
     *
     * @var string
     */
    private $instanceName = null;

    /**
     * Factory constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $instanceName = '\\Althea\\AutoCancelOrder\\Model\\ResourceModel\\Cancel\\Collection'
    )
    {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
    }

    /**
     * {@inheritdoc}
     */
    public function create($orderIds = null, $autoCancelDate = null, $autoCancelStatus = null)
    {
        /** @var \Magento\Sales\Model\ResourceModel\Order\Collection $collection */
        $collection = $this->objectManager->create($this->instanceName);

        if (!is_null($orderIds)) {
            $collection->addFieldToFilter('order_id', array('in' => $orderIds));
        }

        if (!is_null($autoCancelDate)) {
            $collection->addFieldToFilter('autocancel_date', array('lt' => $autoCancelDate));
        }

        if (!is_null($autoCancelStatus)) {
            $collection->addFieldToFilter('autocancel_status', $autoCancelStatus);
        }

        return $collection;
    }
}
