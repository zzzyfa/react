<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Block\Request;

use Amasty\Rma\Model\Request;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\ObjectManagerInterface;
use Magento\Sales\Model\ResourceModel\Order\Collection as OrderCollection;

class History extends \Magento\Framework\View\Element\Template
{
    const MAX_ORDERS_IN_SELECT = 20;

    const MODE_CUSTOMER = 'customer';
    const MODE_GUEST = 'guest';

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    /**
     * @var \Amasty\Rma\Model\Session
     */
    protected $rmaSession;
    /**
     * @var \Amasty\Rma\Helper\Data
     */
    protected $helper;

    /**
     * History constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Amasty\Rma\Helper\Data                          $helper
     * @param \Amasty\Rma\Model\Session                        $rmaSession
     * @param ObjectManagerInterface                           $objectManager
     * @param \Magento\Customer\Model\Session                  $customerSession
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,

        \Amasty\Rma\Helper\Data $helper,
        \Amasty\Rma\Model\Session $rmaSession,
        ObjectManagerInterface $objectManager,
        \Magento\Customer\Model\Session $customerSession,

        array $data = []
    ) {
        $this->objectManager = $objectManager;

        $this->customerSession = $customerSession;
        $this->rmaSession = $rmaSession;

        $this->setData('mode', self::MODE_GUEST);
        $this->setTemplate('Amasty_Rma::guest/history.phtml');

        parent::__construct($context, $data);
        $this->helper = $helper;
    }

    /**
     * @return \Amasty\Rma\Model\ResourceModel\Request\Collection
     */
    public function getCollection()
    {
        if (!$this->hasData('collection')) {
            /** @var \Amasty\Rma\Model\ResourceModel\Request\Collection $requests */
            $collection = $this->objectManager->create('\Amasty\Rma\Model\ResourceModel\Request\Collection');

            $collection
                ->addFieldToSelect('*')
                ->setOrder('created_at', 'desc')
            ;

            $this->addFilter($collection);

            $this->setData('collection', $collection);
        }

        return $this->getData('collection');
    }

    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        /** @var \Magento\Theme\Block\Html\Pager $pager */
        $pager = $this->getLayout()->createBlock(
            '\Magento\Theme\Block\Html\Pager',
            'amrma.customer.order.pager'
        );

        $pager->setCollection($this->getCollection());
        $this->setChild('amrma_pager', $pager);

        return $this;
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    public function getViewUrl(Request $request)
    {
        return $this->_urlBuilder->getUrl(
            'amasty_rma/request/view',
            ['id' => $request->getId()]
        );
    }

    /**
     * @param $orderId
     *
     * @return string
     */
    public function getNewUrl($orderId)
    {
        return $this->_urlBuilder->getUrl(
            'amasty_rma/request/new',
            ['order_id' => $orderId]
        );
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    public function getDeleteUrl(Request $request)
    {
        return $this->_urlBuilder->getUrl(
            'amasty_rma/request/delete',
            ['id' => $request->getId()]
        );
    }

    /**
     * @param AbstractCollection $collection
     */
    protected function addFilter(AbstractCollection $collection)
    {
        if ($this->getData('mode') == self::MODE_CUSTOMER) {
            $collection->addFieldToFilter(
                'customer_id', $this->customerSession->getCustomerId()
            );
        } else {
            $order = $this->rmaSession->getOrder();

            $emailField = $collection instanceof OrderCollection ? 'customer_email' : 'email';
            $collection->addFieldToFilter(
                $emailField, $order->getCustomerEmail()
            );
        }
    }

    /**
     * @return array
     */
    public function getAvailableOrders()
    {
        /** @var OrderCollection $collection */
        $collection = $this->objectManager->create(
            '\Magento\Sales\Model\ResourceModel\Order\Collection'
        );

        $collection->getSelect()
            ->joinLeft(
                ['order_item' => $collection->getTable('sales_order_item')],
                'main_table.entity_id = order_item.order_id',
                []
            );

        $collection->addFieldToFilter('qty_shipped', ['gt' => 0]);
        $collection->getSelect()
            ->group('main_table.entity_id')
            ->limit(self::MAX_ORDERS_IN_SELECT)
        ;

        $this->helper->addTimeConditions($collection);

        $this->addFilter($collection);

        $collection->addOrder('entity_id', 'desc');
        $collection->addFieldToFilter('status', 'complete');

        $tpl = __('Order #%s - %s - %s');

        $result = [];

        /** @var \Magento\Sales\Model\Order $order */
        foreach ($collection as $order) {
            $availableItems = $this->objectManager->get('\Amasty\Rma\Model\Item')
                ->getOrderItems($order->getId(), true);

            if (sizeof($availableItems) == 0)
                continue;

            $result[$order->getId()] = sprintf(
                $tpl,
                $order->getIncrementId(),
                $this->formatDate($order->getData('created_at')),
                $order->formatPrice($order->getData('grand_total'))
            );
        }

        return $result;
    }
}
