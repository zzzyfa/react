<?php

/**
 * Product:       Xtento_CustomTrackers (2.1.0)
 * ID:            pla2jYgPEb1bu8ncBMlGtw6aKM5PQ018LXPJPkLn5XM=
 * Packaged:      2017-06-01T10:42:53+00:00
 * Last Modified: 2016-03-31T08:55:55+00:00
 * File:          app/code/Xtento/CustomTrackers/Model/Shipping/Carrier/Tracker.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\CustomTrackers\Model\Shipping\Carrier;

class Tracker extends AbstractCarrier implements \Magento\Shipping\Model\Carrier\CarrierInterface
{
    /**
     * Tracker constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateFactory
     * @param \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory
     * @param \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory
     * @param \Xtento\CustomTrackers\Helper\Module $moduleHelper
     * @param \Xtento\CustomTrackers\Helper\Track $trackHelper
     * @param string $code
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
        $code = '',
        array $data = []
    ) {
        parent::__construct(
            $scopeConfig,
            $rateErrorFactory,
            $logger,
            $rateFactory,
            $trackFactory,
            $trackStatusFactory,
            $moduleHelper,
            $trackHelper,
            $data
        );
        $this->_code = $code; // Tracker Code, injected via DI
    }

    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }
}
