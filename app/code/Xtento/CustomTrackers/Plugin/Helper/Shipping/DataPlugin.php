<?php

/**
 * Product:       Xtento_CustomTrackers (2.1.0)
 * ID:            pla2jYgPEb1bu8ncBMlGtw6aKM5PQ018LXPJPkLn5XM=
 * Packaged:      2017-06-01T10:42:53+00:00
 * Last Modified: 2016-07-13T11:03:49+00:00
 * File:          app/code/Xtento/CustomTrackers/Plugin/Helper/Shipping/DataPlugin.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\CustomTrackers\Plugin\Helper\Shipping;

use Magento\Shipping\Helper\Data;

class DataPlugin
{
    /**
     * @var \Xtento\CustomTrackers\Helper\Module
     */
    protected $moduleHelper;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * @var \Magento\Framework\Url\EncoderInterface
     */
    protected $urlEncoder;

    /**
     * @var \Xtento\CustomTrackers\Helper\Track
     */
    protected $trackHelper;

    /**
     * @param \Xtento\CustomTrackers\Helper\Module $moduleHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     * @param \Xtento\CustomTrackers\Helper\Track $trackHelper
     */
    public function __construct(
        \Xtento\CustomTrackers\Helper\Module $moduleHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Xtento\CustomTrackers\Helper\Track $trackHelper
    ) {
        $this->moduleHelper = $moduleHelper;
        $this->scopeConfig = $scopeConfig;
        $this->appState = $appState;
        $this->urlEncoder = $urlEncoder;
        $this->trackHelper = $trackHelper;
    }

    /**
     * @param Data $subject
     * @param \Closure $proceed
     * @param $model
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundGetTrackingPopupUrlBySalesModel(Data $subject, \Closure $proceed, $model)
    {
        if (!$this->moduleHelper->isModuleEnabled()) {
            return $proceed($model);
        }
        if ($this->scopeConfig->isSetFlag('customtrackers/advanced/disable_direct_tracking')
            && $this->appState->getAreaCode() === \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE
        ) {
            return $proceed($model);
        }

        $directTrackingUrl = $this->trackHelper->getDirectTrackingUrl($model);
        if ($directTrackingUrl) {
            return $directTrackingUrl;
        }

        return $proceed($model);
    }
}
