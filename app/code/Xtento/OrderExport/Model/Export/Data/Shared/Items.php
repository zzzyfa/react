<?php

/**
 * Product:       Xtento_OrderExport (2.2.8)
 * ID:            pla2jYgPEb1bu8ncBMlGtw6aKM5PQ018LXPJPkLn5XM=
 * Packaged:      2017-06-01T10:42:36+00:00
 * Last Modified: 2017-03-30T12:35:43+00:00
 * File:          app/code/Xtento/OrderExport/Model/Export/Data/Shared/Items.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\OrderExport\Model\Export\Data\Shared;

use Magento\Framework\Exception\NoSuchEntityException;

class Items extends \Xtento\OrderExport\Model\Export\Data\AbstractData
{
    protected $origWriteArray;
    protected $totalCost = 0;

    /**
     * @var \Magento\Sales\Model\Order\ItemFactory
     */
    protected $orderItemFactory;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * @var \Magento\GiftMessage\Model\MessageFactory
     */
    protected $giftMessageFactory;

    /**
     * @var \Magento\Tax\Model\Sales\Order\TaxFactory
     */
    protected $taxFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Option\Value\Collection
     */
    protected $optionValueCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Option\ValueFactory
     */
    protected $optionValueFactory;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Eav\Model\AttributeSetRepository
     */
    protected $attributeSetRepository;

    /**
     * @var \Magento\Catalog\Model\Product\Media\Config
     */
    protected $mediaConfig;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * Items constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Xtento\XtCore\Helper\Date $dateHelper
     * @param \Xtento\XtCore\Helper\Utils $utilsHelper
     * @param \Magento\Sales\Model\Order\ItemFactory $orderItemFactory
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\GiftMessage\Model\MessageFactory $giftMessageFactory
     * @param \Magento\Tax\Model\Sales\Order\TaxFactory $taxFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\Option\Value\CollectionFactory $optionValueCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\Option\ValueFactory $optionValueFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Eav\Model\AttributeSetRepository $attributeSetRepository
     * @param \Magento\Catalog\Model\Product\Media\Config $mediaConfig
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Xtento\XtCore\Helper\Date $dateHelper,
        \Xtento\XtCore\Helper\Utils $utilsHelper,
        \Magento\Sales\Model\Order\ItemFactory $orderItemFactory,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\GiftMessage\Model\MessageFactory $giftMessageFactory,
        \Magento\Tax\Model\Sales\Order\TaxFactory $taxFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Option\Value\CollectionFactory $optionValueCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Option\ValueFactory $optionValueFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Eav\Model\AttributeSetRepository $attributeSetRepository,
        \Magento\Catalog\Model\Product\Media\Config $mediaConfig,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $dateHelper, $utilsHelper, $resource, $resourceCollection, $data);
        $this->orderItemFactory = $orderItemFactory;
        $this->stockRegistry = $stockRegistry;
        $this->giftMessageFactory = $giftMessageFactory;
        $this->taxFactory = $taxFactory;
        $this->optionValueCollectionFactory = $optionValueCollectionFactory;
        $this->optionValueFactory = $optionValueFactory;
        $this->productRepository = $productRepository;
        $this->attributeSetRepository = $attributeSetRepository;
        $this->mediaConfig = $mediaConfig;
        $this->categoryRepository = $categoryRepository;
    }


    public function getConfiguration()
    {
        // Init cache
        if (!isset($this->cache['product_attributes'])) {
            $this->cache['product_attributes'] = [];
        }
        // Return config
        return [
            'name' => 'Item information',
            'category' => 'Shared',
            'description' => 'Export ordered/invoiced/shipped/refunded items of entity.',
            'enabled' => true,
            'apply_to' => [\Xtento\OrderExport\Model\Export::ENTITY_ORDER, \Xtento\OrderExport\Model\Export::ENTITY_INVOICE, \Xtento\OrderExport\Model\Export::ENTITY_SHIPMENT, \Xtento\OrderExport\Model\Export::ENTITY_CREDITMEMO, \Xtento\OrderExport\Model\Export::ENTITY_QUOTE, \Xtento\OrderExport\Model\Export::ENTITY_AWRMA, \Xtento\OrderExport\Model\Export::ENTITY_BOOSTRMA],
        ];
    }

    public function getExportData($entityType, $collectionItem)
    {
        // Set return array
        $returnArray = [];
        $this->writeArray = & $returnArray['items'];
        // Fetch fields to export
        $object = $collectionItem->getObject();
        #$order = $collectionItem->getOrder();
        $items = $object->getAllItems();
        if (empty($items) || (!$this->fieldLoadingRequired('items') && !$this->fieldLoadingRequired('tax_rates') && !$this->fieldLoadingRequired('packages/') && !$this->fieldLoadingRequired('_total_cost'))) {
            return $returnArray;
        }

        // Export item information
        $taxRates = [];
        $taxBaseAmounts = [];
        $itemCount = 0;
        $totalQty = 0;
        $this->totalCost = 0;
        foreach ($items as $item) {
            $orderItem = false;
            // Check if this product type should be exported
            if ($this->getProfile() && $item->getProductType() && in_array($item->getProductType(), explode(",", $this->getProfile()->getExportFilterProductType()))) {
                continue; // Product type should be not exported
            }
            if ($this->getProfile() && !$item->getProductType() && $this->getProfile()->getExportFilterProductType() !== '' && $entityType !== \Xtento\OrderExport\Model\Export::ENTITY_ORDER && $entityType !== \Xtento\OrderExport\Model\Export::ENTITY_QUOTE && $entityType !== \Xtento\OrderExport\Model\Export::ENTITY_AWRMA && $entityType !== \Xtento\OrderExport\Model\Export::ENTITY_BOOSTRMA) {
                // We are not exporting orders, but need to check the product type - thus, need to load the order item.
                $orderItem = $this->orderItemFactory->create()->load($item->getOrderItemId());
                if ($orderItem->getProductType() && in_array($orderItem->getProductType(), explode(",", $this->getProfile()->getExportFilterProductType()))) {
                    continue; // Product type should be not exported
                }
            }
            // Get information from parent item if item price is 0
            /*if ($item->getPrice() == 0 && $item->getParentItem()) {
              $item = $item->getParentItem();
            }*/
            // Export general item information
            $this->writeArray = & $returnArray['items'][];
            $this->origWriteArray = & $this->writeArray;
            $itemCount++;
            if ($entityType == \Xtento\OrderExport\Model\Export::ENTITY_ORDER || $entityType == \Xtento\OrderExport\Model\Export::ENTITY_AWRMA || $entityType == \Xtento\OrderExport\Model\Export::ENTITY_BOOSTRMA) {
                $itemQty = $item->getQtyOrdered();
            } else {
                $itemQty = $item->getQty();
            }
            $totalQty += $itemQty;
            $this->writeValue('qty_ordered', $itemQty); // Legacy
            $this->writeValue('qty', $itemQty);

            $this->writeValue('item_number', $itemCount);
            $this->writeValue('order_product_number', $itemCount); // Legacy
            foreach ($item->getData() as $key => $value) {
                if ($key == 'qty_ordered' || $key == 'qty') continue;
                $this->writeValue($key, $value);
            }

            // Stock level
            if ($this->fieldLoadingRequired('qty_in_stock')) {
                $stockLevel = 0;
                $stockItem = $this->stockRegistry->getStockItem($item->getProductId());
                if ($stockItem->getId()) {
                    $stockLevel = $stockItem->getQty();
                }
                $this->writeValue('qty_in_stock', $stockLevel);
            }

            // (M1) Enterprise Gift Wrapping information
            /*if ($this->fieldLoadingRequired('enterprise_giftwrapping') && $this->utilsHelper->isMagentoEnterprise()) {
                if ($item->getGwId()) {
                    $this->writeArray['enterprise_giftwrapping'] = [];
                    $this->writeArray =& $this->writeArray['enterprise_giftwrapping'];
                    $wrapping = Mage::getModel('enterprise_giftwrapping/wrapping')->load($item->getGwId());
                    if ($wrapping->getId()) {
                        foreach ($wrapping->getData() as $key => $value) {
                            $this->writeValue($key, $value);
                        }
                        $this->writeValue('image_url', $wrapping->getImageUrl());
                    }
                }
            }*/

            // Repeat SKU by qty ordered, i.e. if "test" is ordered twice output test,test
            if ($this->fieldLoadingRequired('sku_repeated_by_qty')) {
                $this->writeValue('sku_repeated_by_qty', implode(",", array_fill(0, $itemQty, $item->getSku())));
            }

            // Add fields of order item for invoice exports
            $taxItem = false;
            if ($entityType !== \Xtento\OrderExport\Model\Export::ENTITY_ORDER && $entityType !== \Xtento\OrderExport\Model\Export::ENTITY_QUOTE && $entityType !== \Xtento\OrderExport\Model\Export::ENTITY_AWRMA && $entityType !== \Xtento\OrderExport\Model\Export::ENTITY_BOOSTRMA && ($this->fieldLoadingRequired('order_item') || $this->fieldLoadingRequired('tax_rates') || $this->fieldLoadingRequired('custom_options'))) {
                $this->writeArray['order_item'] = [];
                $this->writeArray =& $this->writeArray['order_item'];
                if ($item->getOrderItemId()) {
                    if (!$orderItem) {
                        $orderItem = $this->orderItemFactory->create()->load($item->getOrderItemId());
                    }
                    if ($orderItem->getId()) {
                        $taxItem = $orderItem;
                        foreach ($orderItem->getData() as $key => $value) {
                            $this->writeValue($key, $value);
                        }
                    }
                }
                $this->writeArray = & $this->origWriteArray;
                $tempOrigArray = & $this->writeArray;
                if ($this->fieldLoadingRequired('custom_options') && $options = $orderItem->getProductOptions()) {
                    // Export custom options
                    $this->writeCustomOptions($options, $this->origWriteArray, $object, $orderItem->getProductId());
                }
                $this->writeArray =& $tempOrigArray;
            } else {
                $taxItem = $item;
            }

            // Gift message
            if ($this->fieldLoadingRequired('gift_message')) {
                $giftMessageId = $item->getGiftMessageId();
                if (!$giftMessageId && $orderItem) {
                    $giftMessageId = $orderItem->getGiftMessageId();
                }
                $giftMessageModel = $this->giftMessageFactory->create()->load($giftMessageId);
                if ($giftMessageModel->getId()) {
                    $this->writeValue('gift_message_sender', $giftMessageModel->getSender());
                    $this->writeValue('gift_message_recipient', $giftMessageModel->getRecipient());
                    $this->writeValue('gift_message', $giftMessageModel->getMessage());
                } else {
                    $this->writeValue('gift_message_sender', '');
                    $this->writeValue('gift_message_recipient', '');
                    $this->writeValue('gift_message', '');
                }
            }

            // Get parent item
            $parentItem = $item->getParentItem();
            if (!$parentItem && $orderItem) {
                $parentItemId = $orderItem->getParentItemId();
                if ($parentItemId) {
                    $parentItem = $this->orderItemFactory->create()->load($parentItemId);
                }
            }
            // Note: Parent item may be wrong for non-order exports (such as credit memos) as there is no parent_item_id field and thus getParentItem() fails. Theoretically an approach like this could be used, but has never been tested:
            // (M1 code): $parentItem = Mage::getModel('sales/order_creditmemo_item')->load($parentItem->getOrderItemId(), 'order_item_id’);

            // Get bundle price
            $productOptions = $item->getProductOptions();
            if ($parentItem && $parentItem->getProductType() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
                if (!isset($productOptions['bundle_selection_attributes']) && $parentItem) {
                    $productOptions = $parentItem->getProductOptions();
                }
                if (isset($productOptions['bundle_selection_attributes'])) {
                    $bundleOptions = unserialize($productOptions['bundle_selection_attributes']);
                    if (isset($bundleOptions['price'])) {
                        $this->writeValue('is_bundle', true);
                        $this->writeValue('bundle_price', $bundleOptions['price']);
                    }
                }
            }

            if ($this->fieldLoadingRequired('product_options_data') && $productOptions && is_array($productOptions)) {
                $this->writeArray['product_options_data'] = [];
                $this->writeArray = & $this->origWriteArray['product_options_data'];
                foreach ($productOptions as $productOptionKey => $productOptionValue) {
                    if (($productOptionKey == 'giftcard_created_codes' || $productOptionKey == 'giftcard_sent_codes') && is_array($productOptionValue)) {
                        $productOptionValue = implode(",", $productOptionValue);
                    }
                    if (!is_array($productOptionKey) && !is_object($productOptionKey) && !is_object($productOptionValue)) {
                        $this->writeValue($productOptionKey, $productOptionValue);
                    }
                }
                $this->writeArray = & $this->origWriteArray;
            }

            /*if ($this->fieldLoadingRequired('info_buyrequest') && $productOptions && isset($productOptions['info_buyRequest']) && is_array($productOptions['info_buyRequest'])) {
                $this->writeArray['info_buyrequest'] = [];
                $this->writeArray = & $this->origWriteArray['info_buyrequest'];
                foreach ($productOptions['info_buyRequest'] as $productOptionKey => $productOptionValue) {
                    if (!is_array($productOptionKey) && !is_object($productOptionKey) && !is_array($productOptionValue) && !is_object($productOptionValue)) {
                        $this->writeValue($productOptionKey, $productOptionValue);
                    }
                }
                $this->writeArray = & $this->origWriteArray;
            }*/
            if ($this->fieldLoadingRequired('additional_options') && $productOptions && isset($productOptions['additional_options']) && is_array($productOptions['additional_options'])) {
                $this->writeArray['additional_options'] = [];
                foreach ($productOptions['additional_options'] as $additionalOption) {
                    $this->writeArray = & $this->origWriteArray['additional_options'][];
                    foreach ($additionalOption as $productOptionKey => $productOptionValue) {
                        if (!is_array($productOptionKey) && !is_object($productOptionKey) && !is_array($productOptionValue) && !is_object($productOptionValue)) {
                            $this->writeValue($productOptionKey, $productOptionValue);
                        }
                    }
                }
                $this->writeArray = & $this->origWriteArray;
            }
            /*
            if ($this->fieldLoadingRequired('swatch_data')) {
                // "Swatch Data" export
                if (isset($productOptions['info_buyRequest']['swatchData']) && is_array($productOptions['info_buyRequest']['swatchData'])) {
                    $this->writeArray['swatch_data'] = [];
                    foreach ($productOptions['info_buyRequest']['swatchData'] as $swatchId => $swatchData) {
                        $this->writeArray = & $this->origWriteArray['swatch_data'][];
                        foreach ($swatchData as $key => $value) {
                            $this->writeValue($key, $value);
                        }
                    }
                    $this->writeArray = & $this->origWriteArray;
                }
                // End "Swatch Data"
            }*/

            /*if ($item->getProductType() == \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE && $this->fieldLoadingRequired('downloadable_links')) {
                $productOptions = $item->getProductOptions();
                if ($productOptions) {
                    if (isset($productOptions['links']) && is_array($productOptions['links'])) {
                        $this->writeArray['downloadable_links'] = [];
                        $downloadableLinksCollection = Mage::getModel('downloadable/link')->getCollection()
                            ->addTitleToResult()
                            ->addFieldToFilter('`main_table`.link_id', ['in' => $productOptions['links']]);
                        foreach ($downloadableLinksCollection as $downloadableLink) {
                            $this->writeArray = & $this->origWriteArray['downloadable_links'][];
                            foreach ($downloadableLink->getData() as $downloadableKey => $downloadableValue) {
                                $this->writeValue($downloadableKey, $downloadableValue);
                            }
                        }
                        $this->writeArray = & $this->origWriteArray;
                    }
                }
            }*/

            // Save tax information for order
            if ($taxItem && $item->getBaseTaxAmount() > 0 && $taxItem->getTaxPercent() > 0) {
                $taxPercent = str_replace('.', '_', sprintf('%.4f', $taxItem->getTaxPercent()));
                if (!isset($taxRates[$taxPercent])) {
                    $taxRates[$taxPercent] = $item->getBaseTaxAmount();
                    $taxBaseAmounts[$taxPercent] = $item->getBaseRowTotalInclTax() - $item->getBaseDiscountAmount();
                } else {
                    $taxRates[$taxPercent] += $item->getBaseTaxAmount();
                    $taxBaseAmounts[$taxPercent] += $item->getBaseRowTotalInclTax() - $item->getBaseDiscountAmount();
                }
            }

            if ($this->fieldLoadingRequired('_total_cost')) {
                $this->totalCost += ($item->getBaseCost() * $item->getQtyOrdered());
                $this->writeValue('product_total_cost', ($item->getBaseCost() * $item->getQtyOrdered()));
            }

            // Add fields of parent item
            if ($this->fieldLoadingRequired('parent_item') && $parentItem) {
                $this->writeArray['parent_item'] = [];
                $this->writeArray =& $this->writeArray['parent_item'];
                $tempOrigArray = & $this->writeArray;
                foreach ($parentItem->getData() as $key => $value) {
                    $this->writeValue($key, $value);
                }
                // Export parent product options
                if ($this->fieldLoadingRequired('custom_options') && $options = $parentItem->getProductOptions()) {
                    $this->writeCustomOptions($options, $this->writeArray, $object, $parentItem->getProductId());
                }
                $this->writeArray =& $tempOrigArray;
                if ($this->fieldLoadingRequired('product_attributes')) {
                    $this->writeProductAttributes($object, $parentItem, true);
                }
                $this->writeArray =& $tempOrigArray;
            }
            $this->writeArray = & $this->origWriteArray;
            // Export product attributes
            if ($this->fieldLoadingRequired('product_attributes')) {
                $this->writeProductAttributes($object, $item, false);
            }

            $this->writeArray = & $this->origWriteArray;
            // Export product options
            if ($this->fieldLoadingRequired('custom_options') && $options = $item->getProductOptions()) {
                // Export custom options
                $this->writeCustomOptions($options, $this->origWriteArray, $object, $item->getProductId());
                // Export $options["attributes_info"].. maybe?
            }

            // Sample code to get ugiftcert gift certificate information:
            /*
             $giftCerts = Mage::getModel('ugiftcert/cert')->getCollection()->addItemFilter($item->getId());
             if (count($giftCerts)) {
                foreach ($giftCerts as $giftCert) {
                    if (isset($giftCert['cert_number'])) {
                        ...
                    }
                }
             }
             */
        }

        // Sample code to add specific things/amounts as line items:
        /*if ($object->getGiftMessageId() > 0) {
            $giftMessage = Mage::helper('giftmessage/message')->getGiftMessage($object->getGiftMessageId());
            $returnArray['items'][] = array(
                'sku' => 'MESSAGE',
                'qty_ordered' => 1,
                'qty' => 1,
                'price' => 0,
                'discount_percent' => '0',
                'custom_options' => array('custom_option' => array('value' => $giftMessage->getMessage()))
            );
        }*/

        $this->writeArray = & $returnArray;
        $this->writeValue('export_total_qty_ordered', $totalQty);
        $this->writeValue('products_total_cost', $this->totalCost);

        // Add tax amounts of other fees to $taxRates
        // Shipping
        $shippingAmount = 0;
        $shippingTaxAmount = 0;
        if ($entityType == \Xtento\OrderExport\Model\Export::ENTITY_ORDER) {
            $shippingAmount = $object->getData('base_shipping_amount');
            $shippingTaxAmount = $object->getData('base_shipping_tax_amount');
        }
        if ($entityType == \Xtento\OrderExport\Model\Export::ENTITY_INVOICE) {
            $shippingAmount = $object->getData('base_shipping_amount');
            $shippingTaxAmount = $object->getData('base_shipping_tax_amount');
        }
        if ($entityType == \Xtento\OrderExport\Model\Export::ENTITY_CREDITMEMO) {
            $shippingAmount = $object->getData('base_shipping_amount');
            $shippingTaxAmount = $object->getData('base_shipping_tax_amount');
        }
        if ($shippingAmount > 0 && $shippingTaxAmount > 0) {
            $taxPercent = round($shippingTaxAmount / $shippingAmount * 100);
            $taxPercent = str_replace('.', '_', sprintf('%.4f', $taxPercent));
            if (!isset($taxRates[$taxPercent])) {
                $taxRates[$taxPercent] = $shippingTaxAmount;
                $taxBaseAmounts[$taxPercent] = $shippingAmount + $shippingTaxAmount;
            } else {
                $taxRates[$taxPercent] += $shippingTaxAmount;
                $taxBaseAmounts[$taxPercent] += $shippingAmount + $shippingTaxAmount;
            }
        }
        // Cash on Delivery
        $codFee = 0;
        $codFeeTax = 0;
        if ($entityType == \Xtento\OrderExport\Model\Export::ENTITY_ORDER) {
            $codFee = $object->getBaseCodFee();
            $codFeeTax = $object->getBaseCodTaxAmount();
        }
        if ($entityType == \Xtento\OrderExport\Model\Export::ENTITY_INVOICE) {
            $codFee = $object->getOrder()->getData('base_cod_fee_invoiced');
            $codFeeTax = $object->getOrder()->getData('base_cod_tax_amount_invoiced');
        }
        if ($entityType == \Xtento\OrderExport\Model\Export::ENTITY_CREDITMEMO) {
            $codFee = $object->getOrder()->getData('base_cod_fee_refunded');
            $codFeeTax = $object->getOrder()->getData('base_cod_tax_amount_refunded');
        }
        if ($codFee > 0 && $codFeeTax > 0) {
            $taxPercent = round($codFeeTax / $codFee * 100);
            $taxPercent = str_replace('.', '_', sprintf('%.4f', $taxPercent));
            if (!isset($taxRates[$taxPercent])) {
                $taxRates[$taxPercent] = $codFeeTax;
                $taxBaseAmounts[$taxPercent] = $codFee + $codFeeTax;
            } else {
                $taxRates[$taxPercent] += $codFeeTax;
                $taxBaseAmounts[$taxPercent] += $codFee + $codFeeTax;
            }
        }

        // At least provide a 0% tax rate if no tax was found, as no tax was charged then
        if (empty($taxRates)) {
            $taxRates = ['0_0000' => ''];
        }

        // Export tax information
        $this->writeArray['tax_rates'] = [];
        if ($this->fieldLoadingRequired('tax_rates')) {
            $grandTotalInclTax = $object->getGrandTotal();
            foreach ($taxRates as $taxRate => $taxAmount) {
                if ($taxRate == '0_0000') continue;
                $taxBaseAmount = $taxBaseAmounts[$taxRate];
                $taxRate = str_replace('_', '.', $taxRate);
                $this->writeArray = & $returnArray['tax_rates'][];
                $this->writeValue('rate', $taxRate);
                $this->writeValue('amount', $taxAmount);
                $this->writeValue('base', $taxBaseAmount);
                $grandTotalInclTax -= $taxBaseAmount;
            }
            if (isset($taxRates['0_0000'])) {
                $this->writeArray = & $returnArray['tax_rates'][];
                $this->writeValue('rate', '0.0000');
                $this->writeValue('amount', '0.0000');
                $this->writeValue('base', $grandTotalInclTax);
            }
        }
        $this->writeArray = & $returnArray;
        $this->writeArray['order_tax_rates'] = [];
        if ($this->fieldLoadingRequired('order_tax_rates')) {
            $taxRateCollection = $this->taxFactory->create()->getCollection()->loadByOrder($collectionItem->getOrder());
            if ($taxRateCollection->getSize()) {
                foreach ($taxRateCollection as $taxRate) {
                    $this->writeArray = & $returnArray['order_tax_rates'][];
                    foreach ($taxRate->getData() as $key => $value) {
                        if ($key == 'percent') $key = 'rate';
                        $this->writeValue($key, $value);
                    }
                    // Write "base_tax_base" - the base the tax_amount was calculated on
                    $this->writeValue('base_tax_base', ($taxRate->getBaseAmount() / ($taxRate->getPercent() / 100)) + $taxRate->getBaseAmount());
                }
            }
        }

        /*
        $this->writeArray = & $returnArray;
        $packageCollection = Mage::getModel('shipusa/packages')->getCollection()->addQuoteFilter($object->getQuoteId());
        $packageCount = 0;
        $this->writeArray['packages'] = [];
        foreach ($packageCollection as $package) {
            $packageCount++;
            $this->writeArray = & $returnArray['packages'][];
            $this->writeValue('weight', $package->getWeight());
            $this->writeValue('counter', $packageCount);
        }
        */

        // Done
        return $returnArray;
    }


    protected function writeCustomOptions($options, &$writeArray, $object, $productId = null)
    {
        if (isset($options['options'])) {
            $this->writeArray['custom_options'] = [];
            foreach ($options['options'] as $customOption) {
                $optionCount = 0;
                if (isset($customOption['option_value'])) {
                    $optionValues = explode(",", $customOption['option_value']);
                    if (isset($customOption['option_type'])
                        && $customOption['option_type'] !== 'field'
                        && $customOption['option_type'] !== 'area'
                    ) {
                        foreach ($optionValues as $optionValue) {
                            $values = $this->optionValueCollectionFactory->create()
                                ->addPriceToResult($object->getStoreId())
                                ->getValuesByOption($optionValue, $object->getStoreId());
                            if ($values->count()) {
                                $value = $values->getFirstItem();
                                if ($value->getOptionId() && $value->getSku()) {
                                    #$option = $this->optionValueFactory->create()->load($value->getOptionId());
                                    #$value->setOption($option);
                                    $optionCount++;
                                    $this->writeArray = & $writeArray['custom_options'][];
                                    $this->writeValue('name', $customOption['label']);
                                    $this->writeValue('value', $customOption['value']);
                                    $this->writeValue('sku', $value->getSku());
                                    if (/*$option && */$value->getOption() && $value->getOption()->getProduct()) {
                                        $this->writeValue('price', $value->getPrice(true));
                                    }

                                    if (isset($customOption['option_id'])) {
                                        $this->writeValue('option_id', $customOption['option_id']);
                                        $buyRequestQtyKey = 'options_' . $customOption['option_id'] . '_qty';
                                        if (!is_object($options) && is_array($options['info_buyRequest']) && array_key_exists($buyRequestQtyKey, $options['info_buyRequest'])) {
                                            $this->writeValue('qty', $options['info_buyRequest'][$buyRequestQtyKey]);
                                        } else {
                                            $this->writeValue('qty', 1);
                                        }
                                    } else {
                                        $this->writeValue('qty', 1);
                                    }
                                }
                            }
                        }
                    }
                }
                if ($optionCount === 0) {
                    if (!isset($customOption['sku'])) {
                        $customOption['sku'] = '';
                    }
                    if ($productId != NULL && empty($customOption['sku'])) {
                        try {
                            $productDetail = $this->productRepository->getById($productId);
                            $options = $productDetail->getProductOptionsCollection();
                            foreach ($options as $option) {
                                if ($option->getOptionId() == $customOption['option_id']) {
                                    $customOption['sku'] = $option->getSku();
                                }
                            }
                        } catch (NoSuchEntityException $e) {}
                    }

                    $this->writeArray = & $writeArray['custom_options'][];
                    $this->writeValue('name', $customOption['label']);
                    $this->writeValue('value', $customOption['value']);
                    $this->writeValue('sku', $customOption['sku']);
                    if (isset($customOption['option_id'])) {
                        $this->writeValue('option_id', $customOption['option_id']);
                    }
                }
                if (isset($customOption['option_value'])) {
                    $unserializedOptionValues = @unserialize($customOption['option_value']);
                    if ($unserializedOptionValues !== false) {
                        foreach ($unserializedOptionValues as $unserializedOptionKey => $unserializedOptionValue) {
                            $this->writeValue($unserializedOptionKey, $unserializedOptionValue);
                        }
                    }
                }
            }
        }
    }

    protected function writeProductAttributes($object, $item, $isParentItem = false)
    {
        $this->writeArray['product_attributes'] = [];
        $this->writeArray = & $this->writeArray['product_attributes'];
        if (isset($this->cache['product_attributes'][$object->getStoreId()]) && isset($this->cache['product_attributes'][$object->getStoreId()][$item->getProductId()])) {
            // "cached"
            foreach ($this->cache['product_attributes'][$object->getStoreId()][$item->getProductId()] as $attributeCode => $value) {
                $this->writeValue($attributeCode, $value);
            }
        } else {
            try {
                $product = $this->productRepository->getById($item->getProductId(), false, $object->getStoreId());
            } catch (NoSuchEntityException $e) {
                return;
            }
            if ($product->getId()) {
                foreach ($product->getAttributes(null, true) as $productAttribute) {
                    if ($productAttribute instanceof \Magento\Catalog\Model\ResourceModel\Eav\Attribute) {
                        $productAttribute->setStoreId(0); // Admin store
                    }
                    $attributeCode = $productAttribute->getAttributeCode();
                    // Handle attribute set name
                    if ($this->fieldLoadingRequired('attribute_set_name') && $productAttribute->getAttributeCode() == 'attribute_set_id') {
                        try {
                            $attributeSetModel = $this->attributeSetRepository->get(
                                $productAttribute->getFrontend()->getValue($product)
                            );
                            if ($attributeSetModel->getId()) {
                                $this->writeValue('attribute_set_name', $attributeSetModel->getAttributeSetName());
                                $this->cache['product_attributes'][$object->getStoreId()][$item->getProductId()]['attribute_set_name'] = $attributeSetModel->getAttributeSetName();
                            }
                        } catch (NoSuchEntityException $e) {}
                    }
                    if (!$this->fieldLoadingRequired($attributeCode) || $attributeCode == 'category_ids') {
                        continue;
                    }

                    $value = $product->getData($productAttribute->getAttributeCode());
                    if (!empty($value) && $productAttribute->usesSource() && ($options = $productAttribute->getSource()->getAllOptions()) && !empty($options)) {
                        if (sizeof($options) > 0) {
                            foreach ($options as $option) {
                                if (isset($option['value']) && $option['value'] == $value) {
                                    $value = isset($option['label']) ? (string)$option['label'] : $option['value'];
                                }
                            }
                        }
                    }

                    if ($attributeCode == 'image' || $attributeCode == 'small_image' || $attributeCode == 'thumbnail') {
                        $this->writeValue($attributeCode . '_raw', $value);
                        $this->cache['product_attributes'][$object->getStoreId()][$item->getProductId()][$attributeCode . '_raw'] = $value;
                        $this->writeValue($attributeCode, $this->mediaConfig->getMediaUrl($value));
                        $this->cache['product_attributes'][$object->getStoreId()][$item->getProductId()][$attributeCode] = $this->mediaConfig->getMediaUrl($value);
                        continue;
                    }
                    $this->writeValue($attributeCode, $value);
                    $this->cache['product_attributes'][$object->getStoreId()][$item->getProductId()][$attributeCode] = $value;
                    // Get store value
                    if ($this->fieldLoadingRequired($attributeCode . '_store')) {
                        if ($productAttribute instanceof \Magento\Catalog\Model\ResourceModel\Eav\Attribute) {
                            $productAttribute->setStoreId($product->getStoreId());
                        }
                        $value = $product->getData($productAttribute->getAttributeCode());
                        if (!empty($value) && $productAttribute->usesSource() && ($options = $productAttribute->getSource()->getAllOptions()) && !empty($options)) {
                            if (sizeof($options) > 0) {
                                foreach ($options as $option) {
                                    if (isset($option['value']) && $option['value'] == $value) {
                                        $value = isset($option['label']) ? (string)$option['label'] : $option['value'];
                                    }
                                }
                            }
                        }
                        $this->writeValue($attributeCode . '_store', $value);
                        $this->cache['product_attributes'][$object->getStoreId()][$item->getProductId()][$attributeCode . '_store'] = $value;
                    }
                }
                if ($this->fieldLoadingRequired('category_ids')) {
                    $categoryIds = "|" . implode("|", $product->getCategoryIds()) . "|";
                    $this->writeValue('category_ids', $categoryIds);
                    $this->cache['product_attributes'][$object->getStoreId()][$item->getProductId()]['category_ids'] = $categoryIds;
                }
                if ($this->fieldLoadingRequired('category_names')) {
                    if ($product->getCategoryIds()) {
                        $categoryNames = [];
                        foreach ($product->getCategoryIds() as $categoryId) {
                            try {
                                $category = $this->categoryRepository->get($categoryId);
                                if ($category && $category->getId()) {
                                    $categoryNames[] = $category->getName();
                                }
                            } catch (NoSuchEntityException $e) {}
                        }
                        $categoryNames = "|" . implode("|", $categoryNames) . "|";
                        $this->writeValue('category_names', $categoryNames);
                        $this->cache['product_attributes'][$object->getStoreId()][$item->getProductId()]['category_names'] = $categoryNames;
                    }
                }
                if ($this->fieldLoadingRequired('product_url')) {
                    $productUrl = $product->getProductUrl(false);
                    /*if (preg_match("/&/", $productUrl)) {
                        $productUrl = preg_replace("/___store=(.*?)&/", "&", $productUrl);
                    } else {
                        $productUrl = preg_replace("/\?___store=(.*)/", "", $productUrl);
                    }*/
                    $this->writeValue('product_url', $productUrl);
                    $this->cache['product_attributes'][$object->getStoreId()][$item->getProductId()]['product_url'] = $productUrl;
                }
            }
        }
    }
}