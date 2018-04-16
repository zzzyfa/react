<?php

/**
 * Product:       Xtento_XtCore (2.0.7)
 * ID:            pla2jYgPEb1bu8ncBMlGtw6aKM5PQ018LXPJPkLn5XM=
 * Packaged:      2017-06-01T10:43:07+00:00
 * Last Modified: 2015-07-24T20:30:27+00:00
 * File:          app/code/Xtento/XtCore/Model/System/Config/Source/Shipping/Carriers.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\XtCore\Model\System\Config\Source\Shipping;

class Carriers implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Shipping\Model\Config
     */
    protected $shippingConfig;

    /**
     * @var \Xtento\XtCore\Helper\Shipping
     */
    protected $shippingHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Shipping\Model\Config $shippingConfig
     * @param \Xtento\XtCore\Helper\Shipping $shippingHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Shipping\Model\Config $shippingConfig,
        \Xtento\XtCore\Helper\Shipping $shippingHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->shippingConfig = $shippingConfig;
        $this->shippingHelper = $shippingHelper;
        $this->storeManager = $storeManager;
    }

    public function toOptionArray()
    {
        $carriers = [];
        $carriers[] = ['value' => 'custom', 'label' => __('Custom Carrier')];
        foreach ($this->shippingConfig->getAllCarriers() as $carrierCode => $carrierConfig) {
            if ($carrierConfig->isTrackingAvailable()) {
                $carriers[] = [
                    'value' => $carrierCode,
                    'label' => $this->shippingHelper->determineCarrierTitle(
                        $carrierCode,
                        '',
                        $this->storeManager->getStore()->getStoreId()
                    )
                ];
            }
        }
        return $carriers;
    }
}
