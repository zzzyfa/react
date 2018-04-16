<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */

namespace Amasty\Rma\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Request extends AbstractModel
{
    const EXTRA_FIELDS_COUNT = 5;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Core Date
     *
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $coreDate;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;
    
    /**
     * @var Item
     */
    protected $rmaItem;

    /**
     * @var \Magento\Framework\Url
     */
    protected $frontendUrlBuilder;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $datetime;

    /**
     * @var ItemFactory
     */
    private $rmaItemFactory;

    /**
     * @var \Magento\Sales\Api\OrderItemRepositoryInterface
     */
    private $orderItemRepository;

    /**
     * General constructor.
     *
     * @param \Magento\Framework\Model\Context                                               $context
     * @param \Magento\Framework\Registry                                                    $registry
     * @param ResourceModel\Request|\Magento\Framework\Model\ResourceModel\AbstractResource  $resource
     * @param ResourceModel\Request\Collection|\Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param ObjectManagerInterface                                                         $objectManager
     * @param ScopeConfigInterface                                                           $scopeConfig
     * @param StoreManagerInterface                                                          $storeManager
     * @param TransportBuilder                                                               $transportBuilder
     * @param Item                                                                           $rmaItem
     * @param DateTime                                                                       $coreDate
     * @param \Magento\Framework\Url                                                         $frontendUrlBuilder
     * @param array                                                                          $data
     * @param \Magento\Framework\Stdlib\DateTime\DateTime                                    $datetime
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ObjectManagerInterface $objectManager,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder,
        \Magento\Sales\Api\OrderItemRepositoryInterface $orderItemRepository,
        \Amasty\Rma\Model\Item $rmaItem,
        \Amasty\Rma\Model\ItemFactory $rmaItemFactory,
        DateTime $coreDate,
        \Magento\Framework\Url $frontendUrlBuilder,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Amasty\Rma\Model\ResourceModel\Request $resource = null,
        \Amasty\Rma\Model\ResourceModel\Request\Collection $resourceCollection = null,
        array $data = []
    ) {
        $this->objectManager = $objectManager;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->transportBuilder = $transportBuilder;
        $this->coreDate = $coreDate;
        $this->rmaItem = $rmaItem;
        $this->frontendUrlBuilder = $frontendUrlBuilder;
        $this->datetime = $datetime;
        $this->rmaItemFactory = $rmaItemFactory;
        $this->orderItemRepository = $orderItemRepository;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Amasty\Rma\Model\ResourceModel\Request');
    }

    /**
     * @return array
     */
    public function getExtraFields()
    {
        $fields = [];

        for ($i = 1; $i <= self::EXTRA_FIELDS_COUNT; $i++) {
            $label = $this->scopeConfig->getValue(
                'amrma/extra/field_' . $i,
                ScopeInterface::SCOPE_STORE
            );

            if ($label) {
                $fields[$i] = [
                    'value' => $this->getData('field_' . $i),
                    'label' => $label
                ];
            }
        }

        return $fields;
    }

    public function saveRmaItems(array $data)
    {
        $allItems = [];

        if ($this->scopeConfig->isSetFlag('amrma/general/enable_per_item', ScopeInterface::SCOPE_STORE)) {
            $allItems = $this->getData('items');
        } else {
            $items = $this->rmaItem->getOrderItems(
                $this->getData('order_id')
            );

            /** @var \Magento\Sales\Model\Order\Item $item */
            foreach ($items as $item) {
                if ($item->getProductType() == \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE) {
                    $allItems[] = [
                        array_merge(
                            $data,
                            [
                                'order_item_id' => $item->getId(),
                                'qty_requested' => $item->getQtyOrdered(),
                            ]
                        )
                    ];
                }
            }
        }

        $this->_saveItems($allItems);
    }

    protected function _saveItems($allItems)
    {
        foreach ($allItems as $parentId => $rowItem) {
            foreach ($rowItem as $rowId => $dataItem) {
                $items = [$dataItem['order_item_id'] => $dataItem['qty_requested']];

                if (array_key_exists('items', $dataItem)) {
                    $items = $dataItem['items'];
                }

                foreach ($items as $itemId => $qty) {
                    /** @var \Magento\Sales\Model\Order\Item $orderItem */
                    $orderItem = $this->orderItemRepository->get($itemId);

                    if ($orderItem->getId()) {
                        $qty = min($orderItem->getData('qty_ordered'), $qty);

                        if ($qty <= 0) {
                            continue;
                        }

                        /** @var \Amasty\Rma\Model\Item $item */
                        $item = $this->rmaItemFactory->create();
                        $item->setData([
                            'request_id'    => $this->getId(),
                            'order_item_id' => $orderItem->getId(),
                            'product_id'    => $orderItem->getData('product_id'),
                            'sku'           => $orderItem->getData('sku'),
                            'name'          => $orderItem->getData('name'),
                            'qty'           => $qty,
                            'reason'        => $dataItem['reason'],
                            'condition'     => $dataItem['condition'],
                            'resolution'    => $dataItem['resolution']
                        ]);
                        $item->save();
                    }
                }
            }
        }
    }

    /**
     * @return \Amasty\Rma\Model\Status
     */
    public function getStatus()
    {
        if (!$this->hasData('status')) {
            $status = $this->objectManager->create('\Amasty\Rma\Model\Status');

            $status->load($this->getData('status_id'));
            $this->setData('status', $status);
        }

        return $this->_getData('status');
    }

    public function updateItemsQty()
    {
        $items = $this->getData('items');
        if (is_array($items)) {
            foreach ($items as $id => $data) {
                /** @var \Amasty\Rma\Model\Item $item */
                $item = $this->rmaItemFactory->create();
                $item->load($id);
                if ($item->getId() && $item->getData('qty') != $data['qty']) {
                    $item->setData('qty', $data['qty']);
                    $item->save();
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getStatusLabel()
    {
        $store = $this->storeManager->getStore();

        /** @var \Amasty\Rma\Model\Status $status */
        $status = $this->objectManager->create('\Amasty\Rma\Model\Status');
        $status->load($this->getData('status_id'));

        return $status->getStoreLabel($store->getId());
    }

    /**
     * @param \Amasty\Rma\Model\Comment $comment
     * @param bool                      $statusChanged
     * @param bool                      $force
     *
     * @return bool status
     */
    public function sendNotification(Comment $comment, $statusChanged = true, $force = false)
    {
        $status = $this->getStatus();
        $storeId = $this->getData('store_id');
        $commentValue = $comment->getData('value');

        $commentTemplateId = $this->scopeConfig->getValue(
            'amrma/email_templates/admin_comment',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $templateId = $statusChanged
            ?
            $status->getStoreTemplate($storeId)
            :
            $commentTemplateId;

        if ($statusChanged && !$templateId && !empty($commentValue)) {
            $templateId = $commentTemplateId;
        }

        if ($templateId && ($statusChanged || $force)) {
            $recipientEmail = $this->getData('email');

            $sender = [
                'name'  => $this->scopeConfig->getValue('amrma/email/name', ScopeInterface::SCOPE_STORE),
                'email' => $this->scopeConfig->getValue('amrma/email/email', ScopeInterface::SCOPE_STORE)
            ];

            $vars = [
                'request' => $this,
                'store'   => $this->storeManager->getStore($storeId),
                'comment' => $comment,
            ];

            $this->transportBuilder
                ->setTemplateIdentifier($templateId)
                ->setTemplateOptions(['area' => 'frontend', 'store' => $storeId])
                ->setTemplateVars($vars)
                ->setFrom($sender)
                ->addTo($recipientEmail)
            ;

            $transport = $this->transportBuilder->getTransport();
            $transport->sendMessage();

            return true;
        }

        return false;
    }

    /**
     * @param         $templateSetting
     * @param Comment $comment
     *
     * @return bool
     */
    protected function _sendAdminNotification($templateSetting, Comment $comment)
    {
        $storeId = $this->getData('store_id');

        $notifyAdmin = $this->scopeConfig->isSetFlag(
            'amrma/email/notify_admin',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $templateId = $this->scopeConfig->getValue(
            'amrma/email_templates/' . $templateSetting,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if ($templateId && $notifyAdmin) {
            $sender = [
                'name'  => $this->scopeConfig->getValue('amrma/email/name', ScopeInterface::SCOPE_STORE),
                'email' => $this->scopeConfig->getValue('amrma/email/email', ScopeInterface::SCOPE_STORE)
            ];

            $vars = [
                'request' => $this,
                'store'   => $this->storeManager->getStore($storeId),
                'comment' => $comment,
            ];

            $recipientEmail = $this->scopeConfig->getValue(
                'amrma/email/email',
                ScopeInterface::SCOPE_STORE,
                $storeId
            );

            $this->transportBuilder
                ->setTemplateIdentifier($templateId)
                ->setTemplateOptions(['area' => 'frontend', 'store' => $storeId])
                ->setTemplateVars($vars)
                ->setFrom($sender)
                ->addTo($recipientEmail)
                ->setReplyTo(
                    $this->getData('email'),
                    $this->getCustomerName()
                )
            ;

            $transport = $this->transportBuilder->getTransport();
            $transport->sendMessage();

            return true;
        }

        return false;
    }

    /**
     * @param $comment
     *
     * @return bool
     */
    public function sendNotification2admin($comment)
    {
        return $this->_sendAdminNotification('customer_comment', $comment);
    }

    /**
     * @param $comment
     *
     * @return bool
     */
    public function sendNotificationRmaCreated($comment)
    {
        return $this->_sendAdminNotification('rma_created', $comment);
    }

    /**
     * @return string
     */
    public function getCustomerName()
    {
        return $this->getData('customer_firstname') . ' ' . $this->getData('customer_lastname');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getShippedLabel()
    {
        return $this->getData('is_shipped') ? __('Yes') : __('No');
    }

    /**
     * @return bool
     */
    public function allowPrintLabel()
    {
        return $this->isStatusAllowPrintLabel() && !$this->getData('is_shipped');
    }

    /**
     * @return bool
     */
    public function isStatusAllowPrintLabel()
    {
        return $this->getStatus()->getData('allow_print_label');
    }

    /**
     * @return \Magento\Sales\Model\Order|null
     */
    public function getOrder()
    {
        if (!$this->getData('order_id')) {
            return null;
        }

        if (!$this->hasData('order')) {
            /** @var  \Magento\Sales\Model\Order $order */
            $order = $this->objectManager->create('\Magento\Sales\Model\Order');
            $order->load($this->getData('order_id'));
            
            $this->setData('order', $order);
        }
        
        return $this->getData('order');
    }

    /**
     * @return string
     */
    public function getPrintLabelUrl()
    {
        return $this->frontendUrlBuilder->getUrl(
            'amasty_rma/request/export',
            [
                'id' => $this->getId(),
                'code' => $this->getData('code'),
                '_nosid' => true
            ]
        );
    }

    /**
     * @return string
     */
    public function getConfirmShippingUrl()
    {
        return $this->frontendUrlBuilder->getUrl(
            'amasty_rma/request/confirm',
            [
                'id' => $this->getId(),
                'code' => $this->getData('code'),
                '_nosid' => true
            ]
        );
    }

    public function updateLastShow()
    {
        $this->setLastShow($this->datetime->gmtDate());
        $this->save();
    }
}
