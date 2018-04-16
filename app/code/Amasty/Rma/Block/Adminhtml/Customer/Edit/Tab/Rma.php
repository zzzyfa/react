<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Block\Adminhtml\Customer\Edit\Tab;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Ui\Component\Layout\Tabs\TabInterface;

class Rma extends \Magento\Backend\Block\Widget\Grid\Extended implements TabInterface
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;
    /**
     * @var \Magento\Newsletter\Model\ResourceModel\Queue\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Rma constructor.
     *
     * @param \Magento\Backend\Block\Template\Context                   $context
     * @param \Magento\Backend\Helper\Data                              $backendHelper
     * @param \Amasty\Rma\Model\ResourceModel\Request\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry                               $registry
     * @param array                                                     $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Amasty\Rma\Model\ResourceModel\Request\CollectionFactory $collectionFactory,

        \Magento\Framework\Registry $registry,

        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);
        $this->coreRegistry = $registry;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Return Tab label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('RMA');
    }

    /**
     * Return Tab title
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('RMA');
    }

    /**
     * Tab class getter
     *
     * @return string
     */
    public function getTabClass()
    {
        return '';
    }

    /**
     * Return URL link to Tab content
     *
     * @return string
     */
    public function getTabUrl()
    {
        return '';
    }

    /**
     * Tab should be loaded trough Ajax call
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return false;
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return $this->getCustomerId();
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    public function getCustomerId()
    {
        if (!$this->hasData('customer_id')) {
            $this->setData(
                'customer_id',
                $this->coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID)
            );
        }
        
        return $this->getData('customer_id');
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('rmaGrid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('desc');

        $this->setUseAjax(true);

        $this->setEmptyText(__('No RMA Found'));
    }

    /**
     * @Inheritdoc
     */
    public function getRowUrl($item)
    {
        return $this->getUrl('amasty_rma/request/edit', ['id' => $item->getId()]);
    }

    /**
     * @Inheritdoc
     */
    public function getGridUrl()
    {
        return $this->getUrl('amasty_rma/customer/rma', ['_current' => true]);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        /** @var $collection \Amasty\Rma\Model\ResourceModel\Request\Collection */
        $collection = $this->collectionFactory->create()
            ->addFieldToFilter(
                'customer_id', $this->getCustomerId()
            )
            ->addStatusName()
        ;

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            [
                'header' => __('ID'),
                'align' => 'left',
                'index' => 'id',
                'width' => 10,
            ]
        );

        $this->addColumn(
            'increment_id',
            [
                'header' => __('Order #'),
                'index' => 'increment_id',
                'align' => 'center',
            ]
        );

        $this->addColumn(
            'created_at',
            [
                'header' => __('Date'),
                'type' => 'datetime',
                'align' => 'center',
                'index' => 'created_at',
                'gmtoffset' => true,
                'default' => ' ---- ',
            ]
        );

        $this->addColumn(
            'status_name',
            [
                'header' => __('Status'),
                'align' => 'center',
                'index' => 'status_name',
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->canShowTab()) {
            return parent::_toHtml();
        } else {
            return '';
        }
    }
}
