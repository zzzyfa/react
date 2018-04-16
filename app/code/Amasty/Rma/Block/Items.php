<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Block;

use Magento\Framework\ObjectManagerInterface;

class Items extends \Magento\Framework\View\Element\Template
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * History constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param ObjectManagerInterface                           $objectManager
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,

        ObjectManagerInterface $objectManager,

        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->objectManager = $objectManager;
    }

    /**
     * @return \Amasty\Rma\Model\ResourceModel\Item\Collection
     */
    function getItems()
    {
        if (!$this->hasData('items')) {

            /** @var \Amasty\Rma\Model\ResourceModel\Item\Collection $items */
            $items = $this->objectManager->create(
                '\Amasty\Rma\Model\ResourceModel\Item\Collection'
            );

            if ($this->getData('request') && $this->getData('request')->getId()) {
                $items->addFilter('request_id', $this->getData('request')->getId());
            }

            $this->setData('items', $items);
        }

        return $this->getData('items');
    }
}
