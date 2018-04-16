<?php

/**
 * Product:       Xtento_CustomTrackers (2.1.0)
 * ID:            pla2jYgPEb1bu8ncBMlGtw6aKM5PQ018LXPJPkLn5XM=
 * Packaged:      2017-06-01T10:42:53+00:00
 * Last Modified: 2016-04-06T13:19:42+00:00
 * File:          app/code/Xtento/CustomTrackers/Plugin/Model/Shipping/Order/TrackPlugin.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\CustomTrackers\Plugin\Model\Shipping\Order;

use Magento\Shipping\Model\Order\Track;

class TrackPlugin
{
    /**
     * @var \Xtento\CustomTrackers\Helper\Module
     */
    protected $moduleHelper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param \Xtento\CustomTrackers\Helper\Module $moduleHelper
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Xtento\CustomTrackers\Helper\Module $moduleHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->moduleHelper = $moduleHelper;
        $this->registry = $registry;
        $this->scopeConfig = $scopeConfig;
        $this->objectManager = $objectManager;
    }

    /**
     * @param \Magento\Shipping\Model\Order\Track $subject
     * @param $interceptedOutput
     * @return mixed
     */
    public function afterGetNumberDetail(Track $subject, $interceptedOutput)
    {
        if (!$this->moduleHelper->isModuleEnabled()) {
            return $interceptedOutput;
        }
        if ($this->registry->registry('xtDisabled') !== false) {
            return $interceptedOutput;
        }

        if (preg_match('/^tracker/', $subject->getCarrierCode())) {
            // Register Track
            $this->registry->register('xt_current_track', $subject, true);

            // Get carrier instance of custom tracker
            $className = $this->scopeConfig->getValue(
                'customtrackers/' . $subject->getCarrierCode() . '/model',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $subject->getStore()
            );
            if (!$className) {
                return false;
            }
            $carrier = $this->objectManager->create($className);
            $carrier->setId($subject->getCarrierCode());
            if ($subject->getStore()) {
                $carrier->setStore($subject->getStore());
            }

            $trackingInfo = $carrier->getTrackingInfo($subject->getNumber());
            if (!$trackingInfo) {
                return __('No detail for number "%1"', $subject->getNumber());
            }

            return $trackingInfo;
        }
        return $interceptedOutput;
    }
}
