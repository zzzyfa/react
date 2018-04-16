<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Block\Adminhtml\Request\Edit\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\ObjectManagerInterface;

class Items extends \Magento\Backend\Block\Template implements TabInterface
{
    /** @var  \Magento\Framework\Registry */
    protected $registry;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * General constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param ObjectManagerInterface                  $objectManager
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->objectManager = $objectManager;

        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('RMA Items');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('RMA Items');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    public function _construct()
    {
        $this->setTemplate('Amasty_Rma::request/items.phtml');
        parent::_construct();
    }

    public function getRmaItems()
    {
        /** @var \Amasty\Rma\Model\ResourceModel\Item\Collection $collection */
        $collection = $this->objectManager
            ->create('\Amasty\Rma\Model\ResourceModel\Item\Collection');

        $request = $this->registry->registry('amrma_request');

        $collection->addFilter('request_id', $request->getId());

        return $collection;
    }
}
