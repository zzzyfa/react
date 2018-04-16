<?php

/**
 * Product:       Xtento_CustomTrackers (2.1.0)
 * ID:            pla2jYgPEb1bu8ncBMlGtw6aKM5PQ018LXPJPkLn5XM=
 * Packaged:      2017-06-01T10:42:53+00:00
 * Last Modified: 2016-07-13T11:09:24+00:00
 * File:          app/code/Xtento/CustomTrackers/Helper/Track.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\CustomTrackers\Helper;

class Track extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Registry
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    /**
     * @var \Magento\Sales\Api\ShipmentRepositoryInterface
     */
    protected $shipmentRepository;

    /**
     * @var \Magento\Shipping\Model\InfoFactory
     */
    protected $shippingInfoFactory;

    /**
     * Track constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository
     * @param \Magento\Shipping\Model\InfoFactory $shippingInfoFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository,
        \Magento\Shipping\Model\InfoFactory $shippingInfoFactory
    ) {
        $this->registry = $registry;
        $this->localeDate = $localeDate;
        $this->shipmentRepository = $shipmentRepository;
        $this->shippingInfoFactory = $shippingInfoFactory;
        parent::__construct($context);
    }

    /**
     * @param $track
     * @param $carrierConfig
     * @param $trackingNumber
     * @return mixed
     */
    public function getTrackingLink($track, $carrierConfig, $trackingNumber)
    {
        // Get variables:
        $trackDate = $this->localeDate->scopeDate();
        $orderIncrementId = '';
        $firstname = '';
        $lastname = '';
        $countryCode = '';
        $shippingPostcode = '';

        if ($track === null) {
            $track = $this->registry->registry('xt_current_track');
        }
        if ($track !== null) {
            $realShipment = $this->shipmentRepository->get($track->getParentId());
            if ($realShipment->getId()) {
                if ($realShipment->getOrder()) {
                    $orderIncrementId = $realShipment->getOrder()->getIncrementId();
                    if ($shipAddress = $realShipment->getOrder()->getShippingAddress()) {
                        $firstname = $shipAddress->getFirstname();
                        $lastname = $shipAddress->getLastname();
                        $countryCode = $shipAddress->getCountryId();
                        $shippingPostcode = $shipAddress->getPostcode();
                    }
                    if ($track->getCreatedAt()) {
                        $trackDate = $this->localeDate->scopeDate(
                            $realShipment->getOrder()->getStore(),
                            $track->getCreatedAt(),
                            true
                        );
                    }
                }
            }
            $this->registry->unregister('xt_current_track');
        }

        // Return tracking link
        return preg_replace(
            [
                "/#TRACKINGNUMBER#/i",
                "/#ZIP#/i",
                "/#d#/i",
                "/#m#/i",
                "/#y#/",
                "/#Y#/",
                "/#ORDERNUMBER#/i",
                "/#FIRSTNAME#/i",
                "/#LASTNAME#/i",
                "/#COUNTRYCODE#/i"
            ],
            [
                urlencode($trackingNumber),
                urlencode($shippingPostcode),
                $trackDate->format('j'),
                $trackDate->format('n'),
                $trackDate->format('Y'),
                $trackDate->format('y'),
                urlencode($orderIncrementId),
                urlencode($firstname),
                urlencode($lastname),
                urlencode($countryCode)
            ],
            $carrierConfig->getConfigData('url')
        );
    }

    public function getDirectTrackingUrl($model)
    {
        $hash = false;
        if ($model instanceof \Magento\Sales\Model\Order) {
            $hash = $this->urlEncoder->encode("order_id:{$model->getId()}:{$model->getProtectCode()}");
        } elseif ($model instanceof \Magento\Sales\Model\Order\Shipment) {
            $hash = $this->urlEncoder->encode("ship_id:{$model->getId()}:{$model->getProtectCode()}");
        } elseif ($model instanceof \Magento\Sales\Model\Order\Shipment\Track) {
            $hash = $this->urlEncoder->encode("track_id:{$model->getEntityId()}:{$model->getProtectCode()}");
        }
        if ($hash) {
            $shippingInfoModel = $this->shippingInfoFactory->create()->loadByHash($hash);
            $trackingInfo = $shippingInfoModel->getTrackingInfo();
            if (count($trackingInfo) == 1) {
                $trackingModels = array_shift($trackingInfo);
                if (count($trackingModels) == 1) {
                    $tracking = array_shift($trackingModels);
                    if ($tracking instanceof \Magento\Shipping\Model\Tracking\Result\Status
                        && $tracking->hasData('url')
                    ) {
                        return $tracking->getUrl();
                    }
                }
            }
        }
        return '';
    }
}
