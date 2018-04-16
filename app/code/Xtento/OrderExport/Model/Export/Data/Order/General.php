<?php

/**
 * Product:       Xtento_OrderExport (2.2.8)
 * ID:            pla2jYgPEb1bu8ncBMlGtw6aKM5PQ018LXPJPkLn5XM=
 * Packaged:      2017-06-01T10:42:36+00:00
 * Last Modified: 2017-01-05T14:41:26+00:00
 * File:          app/code/Xtento/OrderExport/Model/Export/Data/Order/General.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\OrderExport\Model\Export\Data\Order;

class General extends \Xtento\OrderExport\Model\Export\Data\AbstractData
{
    protected $origWriteArray;

    /**
     * @var \Magento\GiftMessage\Model\MessageFactory
     */
    protected $messageFactory;

    /**
     * General constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Xtento\XtCore\Helper\Date $dateHelper
     * @param \Xtento\XtCore\Helper\Utils $utilsHelper
     * @param \Magento\GiftMessage\Model\MessageFactory $messageFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Xtento\XtCore\Helper\Date $dateHelper,
        \Xtento\XtCore\Helper\Utils $utilsHelper,
        \Magento\GiftMessage\Model\MessageFactory $messageFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $dateHelper, $utilsHelper, $resource, $resourceCollection, $data);
        $this->messageFactory = $messageFactory;
    }


    public function getConfiguration()
    {
        return [
            'name' => 'General order information',
            'category' => 'Order',
            'description' => 'Export extended order information from the sales_flat_order table.',
            'enabled' => true,
            'apply_to' => [
                \Xtento\OrderExport\Model\Export::ENTITY_ORDER,
                \Xtento\OrderExport\Model\Export::ENTITY_INVOICE,
                \Xtento\OrderExport\Model\Export::ENTITY_SHIPMENT,
                \Xtento\OrderExport\Model\Export::ENTITY_CREDITMEMO,
                \Xtento\OrderExport\Model\Export::ENTITY_AWRMA,
                \Xtento\OrderExport\Model\Export::ENTITY_BOOSTRMA
            ],
        ];
    }

    public function getExportData($entityType, $collectionItem)
    {
        // Set return array
        $returnArray = [];
        // Fetch fields to export
        $order = $collectionItem->getOrder();
        if ($entityType == \Xtento\OrderExport\Model\Export::ENTITY_ORDER) {
            $this->writeArray = &$returnArray; // Write directly on order level
        } else {
            $this->writeArray = &$returnArray['order']; // Write on a subnode so the order details can be accessed for invoices/shipments/credit memos
            // Timestamps of creation/update
            if ($this->fieldLoadingRequired('created_at_timestamp')) {
                $this->writeValue(
                    'created_at_timestamp',
                    $this->dateHelper->convertDateToStoreTimestamp($order->getCreatedAt())
                );
            }
            if ($this->fieldLoadingRequired('updated_at_timestamp')) {
                $this->writeValue(
                    'updated_at_timestamp',
                    $this->dateHelper->convertDateToStoreTimestamp($order->getUpdatedAt())
                );
            }
            $this->writeValue('entity_id', $order->getEntityId());
        }
        $this->origWriteArray = &$this->writeArray;

        // Nicer store name
        $this->writeValue('store_name_orig', $order->getStoreName());
        $this->writeValue('store_name', preg_replace('/[^A-Za-z0-9- ]/', ' - ', $order->getStoreName()));

        // General order data
        foreach ($order->getData() as $key => $value) {
            if ($key == 'entity_id' || $key == 'store_name') {
                continue;
            }
            $this->writeValue($key, $value);
        }

        // Sample code to export data from a 3rd party table (M1, must be ported)
        /*if ($order->getId()) {
            $resource = Mage::getSingleton('core/resource');
            $readConnection = $resource->getConnection('core_read');
            //$tableName = $resource->getTableName('catalog/product');
            $query = 'SELECT admin_name FROM salesrep WHERE order_id = ' . (int)$order->getId() . ' LIMIT 1';
            $adminUser = $readConnection->fetchOne($query);
            $this->writeValue('admin_name', $adminUser);
        }*/

        // Last invoice, shipment, credit memo ID
        if ($order->getInvoiceCollection()
            && $order->hasInvoices()
            && ($this->fieldLoadingRequired('invoice_increment_id')
                || $this->fieldLoadingRequired('invoice_created_at_timestamp')
                || $this->fieldLoadingRequired('invoice_updated_at_timestamp')
                || $this->fieldLoadingRequired('invoice_count')
            )
        ) {
            $invoiceCollection = $order->getInvoiceCollection();
            $this->writeValue('invoice_count', $invoiceCollection->getSize());
            if (!empty($invoiceCollection)) {
                $lastInvoice = $invoiceCollection->getLastItem();
                $this->writeValue('invoice_increment_id', $lastInvoice->getIncrementId());
                $this->writeValue(
                    'invoice_created_at_timestamp',
                    $this->dateHelper->convertDateToStoreTimestamp($lastInvoice->getCreatedAt())
                );
                $this->writeValue(
                    'invoice_updated_at_timestamp',
                    $this->dateHelper->convertDateToStoreTimestamp($lastInvoice->getUpdatedAt())
                );
            }
        }
        if ($order->getShipmentsCollection()
            && $order->hasShipments()
            && ($this->fieldLoadingRequired('shipment_increment_id')
                || $this->fieldLoadingRequired('shipment_created_at_timestamp')
                || $this->fieldLoadingRequired('shipment_updated_at_timestamp')
                || $this->fieldLoadingRequired('shipment_count')
            )
        ) {
            $shipmentCollection = $order->getShipmentsCollection();
            $this->writeValue('shipment_count', $shipmentCollection->getSize());
            if (!empty($shipmentCollection)) {
                $lastShipment = $shipmentCollection->getLastItem();
                $this->writeValue('shipment_increment_id', $lastShipment->getIncrementId());
                $this->writeValue(
                    'shipment_created_at_timestamp',
                    $this->dateHelper->convertDateToStoreTimestamp($lastShipment->getCreatedAt())
                );
                $this->writeValue(
                    'shipment_updated_at_timestamp',
                    $this->dateHelper->convertDateToStoreTimestamp($lastShipment->getUpdatedAt())
                );
            }
        }
        if ($order->getCreditmemosCollection()
            && $order->hasCreditmemos()
            && ($this->fieldLoadingRequired('creditmemo_increment_id')
                || $this->fieldLoadingRequired('creditmemo_created_at_timestamp')
                || $this->fieldLoadingRequired('creditmemo_updated_at_timestamp')
                || $this->fieldLoadingRequired('creditmemo_count')
            )
        ) {
            $creditmemoCollection = $order->getCreditmemosCollection();
            $this->writeValue('creditmemo_count', $creditmemoCollection->getSize());
            if (!empty($creditmemoCollection)) {
                $lastCreditmemo = $creditmemoCollection->getLastItem();
                $this->writeValue('creditmemo_increment_id', $lastCreditmemo->getIncrementId());
                $this->writeValue(
                    'creditmemo_created_at_timestamp',
                    $this->dateHelper->convertDateToStoreTimestamp(
                        $lastCreditmemo->getCreatedAt()
                    )
                );
                $this->writeValue(
                    'creditmemo_updated_at_timestamp',
                    $this->dateHelper->convertDateToStoreTimestamp(
                        $lastCreditmemo->getUpdatedAt()
                    )
                );
            }
        }

        // Gift message
        if ($order->getGiftMessageId() && $this->fieldLoadingRequired('gift_message')) {
            $giftMessageModel = $this->messageFactory->create()->load($order->getGiftMessageId());
            if ($giftMessageModel->getId()) {
                $this->writeValue('gift_message_sender', $giftMessageModel->getSender());
                $this->writeValue('gift_message_recipient', $giftMessageModel->getRecipient());
                $this->writeValue('gift_message', $giftMessageModel->getMessage());
            }
        } else {
            $this->writeValue('gift_message_sender', '');
            $this->writeValue('gift_message_recipient', '');
            $this->writeValue('gift_message', '');
        }

        // Serialized gift_cards column on sales/order level
        if ($this->fieldLoadingRequired('giftcards')) {
            $this->writeArray['giftcards'] = [];
            $giftCardsArray = &$this->writeArray['giftcards'];
            if ($order->getData('gift_cards')) {
                #$giftCardSerialized = 'a:1:{i:0;a:5:{s:1:"i";s:1:"1";s:1:"c";s:12:"01S003ZRDKQD";s:1:"a";d:10.99;s:2:"ba";d:10.99;s:10:"authorized";d:10.99;}}';
                $giftCardSerialized = $order->getData('gift_cards');
                $giftCards = @unserialize($giftCardSerialized);
                if (!empty($giftCards) && is_array($giftCards)) {
                    foreach ($giftCards as $giftCard) {
                        $this->writeArray = &$giftCardsArray[];
                        foreach ($giftCard as $key => $value) {
                            $this->writeValue($key, $value);
                        }
                    }
                }
            }
            $this->writeArray = &$this->origWriteArray;
        }

        // Done
        return $returnArray;
    }
}