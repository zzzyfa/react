<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */

namespace Amasty\Rma\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Sales\Model\Order\Item as OrderItem;
use Magento\Catalog\Model\Product\Type as ProductType;

class Item extends AbstractModel
{
    protected $availableProductTypes = [
        \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE,
        \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE,

        \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE,
        \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE,
    ];
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var \Amasty\Rma\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * Item constructor.
     *
     * @param \Magento\Framework\Model\Context                   $context
     * @param \Magento\Framework\Registry                        $registry
     * @param ResourceModel\Item|null                            $resource
     * @param ResourceModel\Item\Collection|null                 $resourceCollection
     * @param \Magento\Framework\ObjectManagerInterface          $objectManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Amasty\Rma\Helper\Data                            $helper
     * @param \Magento\Framework\Message\ManagerInterface        $messageManager
     * @param array                                              $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Amasty\Rma\Model\ResourceModel\Item $resource = null,
        \Amasty\Rma\Model\ResourceModel\Item\Collection $resourceCollection = null,

        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Amasty\Rma\Helper\Data $helper,
        \Magento\Framework\Message\ManagerInterface $messageManager,

        array $data = []
    )
    {
        parent::__construct($context, $registry, $resource, $resourceCollection);
        $this->objectManager = $objectManager;
        $this->scopeConfig = $scopeConfig;
        $this->helper = $helper;
        $this->messageManager = $messageManager;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Amasty\Rma\Model\ResourceModel\Item');
    }

    /**
     * @return \Magento\Sales\Model\ResourceModel\Order\Item\Collection
     */
    public function getSalesOrderItemCollection()
    {
        /** @var \Magento\Sales\Model\ResourceModel\Order\Item\Collection $collection */
        $collection = $this->objectManager->create(
            '\Magento\Sales\Model\ResourceModel\Order\Item\Collection'
        );

        $collection
            ->addFieldToFilter('product_type', ['in' => $this->availableProductTypes]);

        $this->helper->addTimeConditions($collection);

        return $collection;
    }

    /**
     * @param $orderId
     *
     * @return \Magento\Sales\Model\ResourceModel\Order\Item\Collection
     */
    public function getOrderItemsCollection($orderId)
    {
        $collection = $this->getSalesOrderItemCollection();
        $collection->addFieldToFilter('order_id', $orderId);
        return $collection;
    }

    /**
     * @param      $orderId
     *
     * @param bool $availableOnly
     *
     * @return \Magento\Sales\Model\ResourceModel\Order\Item\Collection
     */
    public function getOrderItems($orderId, $availableOnly = false)
    {
        $orderItemsCollection = $this->getOrderItemsCollection($orderId);

        $orderItemsCollection->getSelect()
            ->columns(['rma_qty' => 'SUM(rma_item.qty)'])
            ->joinLeft(
                ['rma_item' => $orderItemsCollection->getTable('amasty_amrma_item')],
                'rma_item.order_item_id = main_table.item_id',
                []
            )
            ->group('main_table.item_id');

        try {
            /** @var OrderItem $item */
            foreach ($orderItemsCollection as $item) {
                $item->setName($this->getProductName($item));
                if (!$item->getProduct()) {
                    $this->messageManager->addErrorMessage(__('Please, note, item(s) existing in your order was deleted from catalog'));
                    continue;
                }
                $allowAttribute = $item->getProduct()->getData('allow_for_rma');
                $allowAttribute = ($allowAttribute == false && !is_null($allowAttribute)) ? false : true;
                $item->setData('allow_for_rma', $allowAttribute);

                $item->setData(
                    'available_qty',
                    $item->getQtyOrdered() - $item->getQtyRefunded() - $item->getQtyCanceled() - $item->getData('rma_qty')
                );

                if (
                    $availableOnly && $item->getData('available_qty') <= 0
                    || // Exclude all complex items except bundles
                    $item->getData('has_children') && ($item->getProductType() != ProductType::TYPE_BUNDLE)
                    || // Exclude children of bundle products
                    $item->getParentItem() && $item->getParentItem()->getProductType() == ProductType::TYPE_BUNDLE
                    ||
                    $item->getData('allow_for_rma') === false
                ) {
                    $orderItemsCollection->removeItemByKey($item->getId());
                }
            }
        } catch (\Error $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $orderItemsCollection;
    }

    /**
     * @param OrderItem $item
     *
     * @return string
     */
    public function getProductName(OrderItem $item)
    {
        $name = $item->getName();
        $result = [];

        if ($options = $item->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }

            if (!empty($result)) {
                $implode = [];
                foreach ($result as $val) {
                    $implode[] = isset($val['print_value']) ? $val['print_value'] : $val['value'];
                }
                return $name . ' (' . implode(', ', $implode) . ')';
            }
        }
        return $name;
    }
}
