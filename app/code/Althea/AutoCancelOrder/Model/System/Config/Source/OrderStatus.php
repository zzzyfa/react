<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Althea\AutoCancelOrder\Model\System\Config\Source;

class OrderStatus implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var array
     */
    protected $_options;

    protected $statusCollectionFactory;

    /**
     * Initialize the options array
     */
    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory $statusCollectionFactory
    )
    {
        $this->statusCollectionFactory = $statusCollectionFactory;

        $status = $this->getStatusOptions();

        $this->_options = $status;

//        $this->_options = [
//            ['value' => 1, 'label' => 'Unit Price'],
//            ['value' => 2, 'label' => 'Row Total'],
//            ['value' => 3, 'label' => 'Total'],
//        ];
    }

    /**
     * Get status options
     *
     * @return array
     */
    public function getStatusOptions()
    {
        $options = $this->statusCollectionFactory->create()->toOptionArray();
        return $options;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_options;
    }
}
