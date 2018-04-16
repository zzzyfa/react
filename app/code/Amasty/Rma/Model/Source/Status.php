<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Model\Source;

use Magento\Framework\DataObject;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Option\ArrayInterface;

class Status extends DataObject implements ArrayInterface
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * General constructor.
     *
     * @param ObjectManagerInterface $objectManager
     * @param array                                     $data
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->objectManager = $objectManager;

        parent::__construct($data);
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->hasData('option_array')) {
            /** @var \Amasty\Rma\Model\ResourceModel\Status\Collection $collection */
            $collection = $this->objectManager->create(
                '\Amasty\Rma\Model\ResourceModel\Status\Collection'
            );

            $collection
                ->addFilter('is_active', 1)
                ->addLabel()
                ->sortByOrder()
                ->load();

            $options = $collection->toOptionArray();

            $this->setData('option_array', $options);
        }

        return $this->getData('option_array');
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function asArray()
    {
        $result = [];

        $options = $this->toOptionArray();

        foreach ($options as $option) {
            $result[$option['value']] = $option['label'];
        }

        return $result;
    }
}
