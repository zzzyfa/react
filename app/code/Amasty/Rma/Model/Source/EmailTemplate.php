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
use Amasty\Rma\Model\Status\Template as StatusTemplate;

class EmailTemplate extends DataObject implements ArrayInterface
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
            /** @var \Magento\Email\Model\ResourceModel\Template\Collection $templateCollection */
            $templateCollection = $this->objectManager->create(
                '\Magento\Email\Model\ResourceModel\Template\Collection'
            );

            $templateCollection
                ->addFilter('orig_template_code', StatusTemplate::TEMPLATE_CODE)
                ->load();

            $options = $templateCollection->toOptionArray();

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
        $result = ['' => ' '];

        $options = $this->toOptionArray();

        foreach ($options as $option) {
            $result[$option['value']] = $option['label'];
        }

        return $result;
    }
}
