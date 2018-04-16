<?php

/**
 * Product:       Xtento_CustomTrackers (2.1.0)
 * ID:            pla2jYgPEb1bu8ncBMlGtw6aKM5PQ018LXPJPkLn5XM=
 * Packaged:      2017-06-01T10:42:53+00:00
 * Last Modified: 2016-05-06T12:06:27+00:00
 * File:          app/code/Xtento/CustomTrackers/Plugin/Model/Shipping/ConfigPlugin.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\CustomTrackers\Plugin\Model\Shipping;

use Magento\Shipping\Model\Config;

class ConfigPlugin
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
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @param \Xtento\CustomTrackers\Helper\Module $moduleHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Xtento\CustomTrackers\Helper\Module $moduleHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->moduleHelper = $moduleHelper;
        $this->scopeConfig = $config;
        $this->registry = $registry;
        $this->objectManager = $objectManager;
        $this->logger = $logger;
        $this->request = $request;
    }

    /**
     * Get all active carriers, remove disabled ones and add our custom ones
     *
     * @param Config $subject
     * @param string $interceptedOutput
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    // Currently not required
    /*public function aroundGetActiveCarriers(Config $subject, \Closure $proceed, $store = null)
    {
        $originalCarriers = $proceed($store);

        if (!$this->moduleHelper->isModuleEnabled()) {
            return $originalCarriers;
        }
        if ($this->registry->registry('xtDisabled') !== false) {
            return $originalCarriers;
        }

        // Remove disabled carriers
        $disabledCarriers = explode(
            ",",
            $this->scopeConfig->getValue(
                'customtrackers/general/disabled_carriers',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $store
            )
        );
        foreach ($originalCarriers as $code => $carrierConfig) {
            if (in_array($code, $disabledCarriers)) {
                unset($originalCarriers[$code]);
            }
        }

        // Get new trackers
        $carriers = array();
        foreach ($this->scopeConfig->getValue(
                     'customtrackers',
                     \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                     $store
                 ) as $carrierCode => $carrierConfig) {
            if ($carrierCode == 'general') {
                continue;
            }
            if ($this->scopeConfig->isSetFlag(
                'customtrackers/' . $carrierCode . '/active',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $store
            )
            ) {
                $model = $this->getCarrier($carrierCode, $carrierConfig, $store);
                if ($model) {
                    $carriers[$carrierCode] = $model;
                }
            }
        }
        return array_merge($originalCarriers, $carriers);
    }*/

    /**
     * Get all carriers, removed disabled ones and add our custom ones
     *
     * @param Config $subject
     * @param \Closure $proceed
     * @param null $store
     *
     * @return array
     */
    public function aroundGetAllCarriers(Config $subject, \Closure $proceed, $store = null)
    {
        $originalCarriers = $proceed($store);

        if (!$this->moduleHelper->isModuleEnabled()) {
            return $originalCarriers;
        }
        if ($this->registry->registry('xtDisabled') !== false) {
            return $originalCarriers;
        }

        // Remove disabled carriers, except if viewing the custom tracker configuration
        if (($this->request->getControllerName() == 'system_config' &&
                $this->request->getParam('section') == 'customtrackers') === false
        ) {
            $disabledCarriers = explode(
                ",",
                $this->scopeConfig->getValue(
                    'customtrackers/general/disabled_carriers',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $store
                )
            );
            foreach ($originalCarriers as $code => $carrierConfig) {
                if (in_array($code, $disabledCarriers)) {
                    unset($originalCarriers[$code]);
                }
            }
        }

        /* Get new trackers */
        $carriers = [];
        foreach ($this->scopeConfig->getValue(
            'customtrackers',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        ) as $carrierCode => $carrierConfig) {
            if ($carrierCode == 'general') {
                continue;
            }
            $model = $this->getCarrier($carrierCode, $carrierConfig, $store);
            if ($model) {
                $carriers[$carrierCode] = $model;
            }
        }
        return array_merge($originalCarriers, $carriers);
    }

    protected function getCarrier($code, $config, $store = null)
    {
        if (!isset($config['model'])) {
            return false;
        }

        /**
         * Added protection from not existing models usage.
         * Related with module uninstall process
         */
        try {
            $carrier = $this->objectManager->create($config['model']);
        } catch (\Exception $e) {
            $this->logger->warning($e->getMessage());
            return false;
        }
        // More protection.
        if (!$carrier) {
            return false;
        }
        $carrier->setId($code)->setStore($store);
        return $carrier;
    }
}
