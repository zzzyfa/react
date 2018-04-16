<?php

/**
 * Product:       Xtento_GridActions (2.1.1)
 * ID:            pla2jYgPEb1bu8ncBMlGtw6aKM5PQ018LXPJPkLn5XM=
 * Packaged:      2017-06-01T10:43:07+00:00
 * Last Modified: 2016-05-30T19:59:22+00:00
 * File:          app/code/Xtento/GridActions/Ui/Component/Listing/Column/CarrierTrack.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\GridActions\Ui\Component\Listing\Column;

use Magento\Framework\Escaper;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class CarrierTrack
 * @package Xtento\GridActions\Ui\Component\Listing\Column
 */
class CarrierTrack extends Column
{
    /**
     * Escaper
     *
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Magento\Shipping\Model\Config
     */
    protected $shippingConfig;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Shipping\Helper\Data
     */
    protected $shippingHelper;

    /**
     * @var \Xtento\GridActions\Helper\Module
     */
    protected $moduleHelper;

    public function toHtml()
    {
        return "";
        parent::toHtml(); // TODO: Change the autogenerated stub
    }

    /**
     * CarrierTrack constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Escaper $escaper
     * @param \Magento\Shipping\Model\Config $shippingConfig
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Shipping\Helper\Data $shippingHelper
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Xtento\GridActions\Helper\Module $moduleHelper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Escaper $escaper,
        \Magento\Shipping\Model\Config $shippingConfig,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Shipping\Helper\Data $shippingHelper,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Xtento\GridActions\Helper\Module $moduleHelper,
        array $components = [],
        array $data = []
    ) {
        $this->escaper = $escaper;
        $this->shippingConfig = $shippingConfig;
        $this->scopeConfig = $scopeConfig;
        $this->shippingHelper = $shippingHelper;
        $this->orderFactory = $orderFactory;
        $this->moduleHelper = $moduleHelper;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$this->getData('name')] = $this->prepareItem($item);
            }
        }

        return $dataSource;
    }

    /**
     * Get data
     *
     * @param array $item
     * @return string
     */
    protected function prepareItem(array $item)
    {
        $html = '';
        if (!$this->moduleHelper->isModuleEnabled()) {
            return $html;
        }

        $orderId = $item['entity_id'];
        /** @var $order \Magento\Sales\Model\Order */
        $order = $this->orderFactory->create()->load($orderId);
        if (!$order->getId()) {
            return $html;
        }

        if (!$order->canShip() && $order->getStatus() !== \Magento\Sales\Model\Order::STATE_CANCELED
            && $order->getStatus() !== \Magento\Sales\Model\Order::STATE_CLOSED
        ) {
            // Order has been shipped. Display shipping carriers.
            $html .= $this->getTrackCarriers($order);
            $html .= '<br/>';
            // Tracking numbers
            $html .= $this->getTrackNumbers($order);

            if ($this->scopeConfig->isSetFlag('gridactions/general/add_trackingnumber_from_grid_shipped')) {
                if ($order->getTracksCollection()->count() > 0) {
                    $html .= '<br/>';
                }
                $html .= $this->getTrackingInput($order);
            }
        } else {
            if ($order->canShip()) {
                // Order has not yet been shipped. Display input + drop down.
                $html .= $this->getCarrierDropdown($order);
                $html .= '<br/>';
                $html .= $this->getTrackingInput($order);
            }
        }

        return $html;
    }

    /**
     * @param $order
     * @return string
     */
    protected function getTrackNumbers($order)
    {
        $html = '';
        $trackingNumbers = [];
        $trackingUrl = $this->shippingHelper->getTrackingPopupUrlBySalesModel($order);
        $tracks = $order->getTracksCollection();
        foreach ($tracks as $track) {
            $trackingNumbers[] = '<a href="#" onclick="window.open(\'' . $trackingUrl . '\',\'trackorder\',\'' .
                'width=800,height=600,left=0,top=0,resizable=yes,scrollbars=yes\')" >' . $this->escaper->escapeHtml(
                $track->getTrackNumber()
            ) . '</a>';
        }
        $html .= implode(', ', $trackingNumbers);
        return $html;
    }

    /**
     * @param $order
     * @return string
     */
    protected function getTrackCarriers($order)
    {
        $carriers = [];
        $tracks = $order->getTracksCollection();
        foreach ($tracks as $track) {
            $carriers[] = $track->getTitle();
        }
        $html = implode(', ', $carriers);

        if ($this->scopeConfig->isSetFlag('gridactions/general/add_trackingnumber_from_grid_shipped')) {
            if (count($tracks) > 0) {
                $html .= '<br/>';
            }
            $html .= $this->getCarrierDropdown($order);
        }
        return $html;
    }

    /**
     * @param $order
     * @return string
     */
    protected function getTrackingInput($order)
    {
        return '<input name="tracking-input-' . $order->getId() . '" rel="' . $order->getId() .
            '" class="input-text tracking-input" value="" style="width:100%; max-width:200px;"' .
            'onclick="xtentoOnClickJs(this)"/>';
    }

    /**
     * @param $order
     * @return string
     */
    protected function getCarrierDropdown($order)
    {
        $html = '';
        try {
            $validCarriers = $this->getCarriers($order->getStoreId());
            if ($validCarriers) {
                $colId = 'carrier-selector';
                $html = '<select name="' . $colId . '-' . $order->getId() . '" rel="' . $order->getId() .
                    '" class="' . $colId . '" style="width: 100%; max-width:200px;" onchange="xtentoOnClickJs(this)">';
                foreach ($validCarriers as $code => $label) {
                    $selected = (($code == $this->scopeConfig->getValue(
                        'gridactions/general/default_carrier'
                    )) ? ' selected="selected"' : '');
                    $html .= '<option ' . $selected . ' value="' . $this->escaper->escapeHtml(
                        $code
                    ) . '">' . $this->escaper->escapeHtml($label) . '</option>';
                }
                $html .= '</select>';
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $html;
    }

    /**
     * Retrieve carriers
     *
     * @param $storeId
     * @return array
     */
    protected function getCarriers($storeId)
    {
        $carriers = [];
        $carrierInstances = $this->shippingConfig->getAllCarriers($storeId);
        $carriers['custom'] = __('Custom Value');
        foreach ($carrierInstances as $code => $carrier) {
            if ($carrier->isTrackingAvailable()) {
                $carriers[$code] = $carrier->getConfigData('title');
            }
        }
        return $carriers;
    }
}
