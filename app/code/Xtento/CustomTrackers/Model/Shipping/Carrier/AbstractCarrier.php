<?php

/**
 * Product:       Xtento_CustomTrackers (2.1.0)
 * ID:            pla2jYgPEb1bu8ncBMlGtw6aKM5PQ018LXPJPkLn5XM=
 * Packaged:      2017-06-01T10:42:53+00:00
 * Last Modified: 2016-04-06T11:34:00+00:00
 * File:          app/code/Xtento/CustomTrackers/Model/Shipping/Carrier/AbstractCarrier.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\CustomTrackers\Model\Shipping\Carrier;

abstract class AbstractCarrier extends \Magento\Shipping\Model\Carrier\AbstractCarrier
{
    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $rateFactory;

    /**
     * @var \Xtento\CustomTrackers\Helper\Module
     */
    protected $moduleHelper;

    /**
     * @var \Magento\Shipping\Model\Tracking\ResultFactory
     */
    protected $trackFactory;

    /**
     * @var \Magento\Shipping\Model\Tracking\Result\StatusFactory
     */
    protected $trackStatusFactory;

    /**
     * @var \Xtento\CustomTrackers\Helper\Track
     */
    protected $trackHelper;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateFactory
     * @param \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory
     * @param \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory
     * @param \Xtento\CustomTrackers\Helper\Module $moduleHelper
     * @param \Xtento\CustomTrackers\Helper\Track $trackHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateFactory,
        \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory,
        \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory,
        \Xtento\CustomTrackers\Helper\Module $moduleHelper,
        \Xtento\CustomTrackers\Helper\Track $trackHelper,
        array $data = []
    ) {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
        $this->rateFactory = $rateFactory;
        $this->moduleHelper = $moduleHelper;
        $this->trackFactory = $trackFactory;
        $this->trackStatusFactory = $trackStatusFactory;
        $this->trackHelper = $trackHelper;
    }


    public function collectRates(\Magento\Quote\Model\Quote\Address\RateRequest $request)
    {
        // Not used for shipping.. so just return an empty result.
        return false;
        //return $this->rateFactory->create();
    }

    public function getConfigData($field)
    {
        if (empty($this->_code)) {
            return false;
        }
        $path = 'customtrackers/' . $this->_code . '/' . $field;
        return $this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore());
    }

    public function getConfigFlag($field)
    {
        if (empty($this->_code)) {
            return false;
        }
        $path = 'customtrackers/' . $this->_code . '/' . $field;
        return $this->_scopeConfig->isSetFlag(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getStore()
        );
    }

    public function isTrackingAvailable()
    {
        if (!$this->moduleHelper->isModuleEnabled()) {
            return false;
        }
        if (!$this->isActive()) {
            return false;
        }
        return true;
    }

    /**
     * Get tracking information
     *
     * @param string $tracking
     * @return string|false
     * @api
     */
    public function getTrackingInfo($tracking)
    {
        if (!is_array($tracking)) {
            $tracking = [$tracking];
        }
        $result = $this->getTracking($tracking);

        if ($result instanceof \Magento\Shipping\Model\Tracking\Result) {
            $trackings = $result->getAllTrackings();
            if ($trackings) {
                return $trackings[0];
            }
        } elseif (is_string($result) && !empty($result)) {
            return $result;
        }

        return false;
    }

    /**
     * @param $trackings
     * @return mixed
     */
    public function getTracking($trackings)
    {
        $result = $this->trackFactory->create();

        foreach ($trackings as $trackingNumber) {
            $tracking = $this->trackStatusFactory->create();
            $tracking->setCarrier($this->_code);
            $tracking->setCarrierTitle($this->getConfigData('title'));
            $tracking->setTracking($trackingNumber);
            $tracking->setPopup(true);
            $tracking->setUrl($this->trackHelper->getTrackingLink(null, $this, $trackingNumber));
            $result->append($tracking);
        }

        return $result;
    }
}
