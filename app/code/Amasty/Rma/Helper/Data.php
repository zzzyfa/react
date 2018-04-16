<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */

namespace Amasty\Rma\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\MediaStorage\Model\File\Uploader;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\ResourceModel\Collection\AbstractCollection;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Data extends AbstractHelper
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;
    /**
     * @var \Magento\Framework\Locale\ListsInterface
     */
    protected $localeLists;
    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $regionFactory;
    /**
     * @var DateTime
     */
    protected $coreDate;

    /**
     * @var \Amasty\Base\Model\Serializer
     */
    private $serializer;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Framework\Locale\ListsInterface $localeLists
     * @param DateTime $coreDate
     * @param \Amasty\Base\Model\Serializer $serializer
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Framework\Locale\ListsInterface $localeLists,
        DateTime $coreDate,
        \Amasty\Base\Model\Serializer $serializer
    ) {
        parent::__construct($context);
        $this->objectManager = $objectManager;
        $this->localeLists = $localeLists;
        $this->regionFactory = $regionFactory;
        $this->coreDate = $coreDate;
        $this->serializer = $serializer;
    }

    public function getUpload($name = 'file')
    {
        try {
            /** @var \Magento\MediaStorage\Model\File\Uploader $upload */
            $upload = $this->objectManager->create(
                '\Magento\MediaStorage\Model\File\Uploader', ['fileId' => $name]
            );

            $upload->setAllowRenameFiles(true);
        } catch (\Exception $e) {
            if ($e->getCode() == Uploader::TMP_NAME_EMPTY) {
                return false;
            } else {
                throw $e;
            }
        }

        return $upload;
    }

    public function canCreateRma(Order $order, &$message = false)
    {
        if ($order->getStatus() != 'complete') {
            $message = __('Order is not completed');
            return false;
        }

        $minAllowedDays = $this->scopeConfig->getValue(
            'amrma/general/min_days', ScopeInterface::SCOPE_STORE
        );

        if ($minAllowedDays) {
            $t = $this->coreDate->timestamp() - (60 * 60 * 24 * $minAllowedDays);
            if (strtotime($order->getCreatedAt()) > $t) {

                $message = __('Minimal number of days is not passed (%1)', $minAllowedDays);
                return false;
            }
        }

        $maxAllowedDays = $this->scopeConfig->getValue(
            'amrma/general/max_days', ScopeInterface::SCOPE_STORE
        );

        if ($maxAllowedDays) {
            $t = $this->coreDate->timestamp() - (60 * 60 * 24 * $maxAllowedDays);
            if (strtotime($order->getCreatedAt()) < $t) {

                $message = __('Maximal number of days is passed (%1)', $maxAllowedDays);
                return false;
            }
        }

        $allowMultipleRequests = $this->scopeConfig->isSetFlag(
            'amrma/general/multiple_requests',
            ScopeInterface::SCOPE_STORE
        );

        if (!$allowMultipleRequests) {
            $request = $this->objectManager->create('\Amasty\Rma\Model\Request');

            $request->load($order->getId(), 'order_id');

            if ($request->getId()) {
                $message = __('Multiple requests are not allowed');
                return false;
            }
        }

        $items = $this->objectManager->get('\Amasty\Rma\Model\Item')
            ->getOrderItems($order->getId(), true);

        $counterOfShippedItems = 0;
        foreach ($order->getAllVisibleItems() as $orderItem) {
            $counterOfShippedItems += $orderItem->getQtyShipped();
        }

        if ($counterOfShippedItems == 0) {
            $message = __('There are no shipped products in this order');
            return false;
        }

        if ($order->getTotalItemCount() == 0) {
            $message = __('No suitable items found in this order');
            return false;
        }

        if ($items->count() == 0) {
            $message = __('There are no products allowed for RMA in this order');
            return false;
        }

        return true;
    }

    public function getReturnAddress()
    {
        $useDefault = $this->scopeConfig->isSetFlag(
            'amrma/shipping/default', ScopeInterface::SCOPE_STORE
        );

        if ($useDefault) {
            $address = [];

            $country = $this->scopeConfig->getValue(
                Shipment::XML_PATH_STORE_COUNTRY_ID, ScopeInterface::SCOPE_STORE
            );
            $state = $this->scopeConfig->getValue(
                Shipment::XML_PATH_STORE_REGION_ID, ScopeInterface::SCOPE_STORE
            );
            $postcode = $this->scopeConfig->getValue(
                Shipment::XML_PATH_STORE_ZIP, ScopeInterface::SCOPE_STORE
            );
            $city = $this->scopeConfig->getValue(
                Shipment::XML_PATH_STORE_CITY, ScopeInterface::SCOPE_STORE
            );
            $street_line1 = $this->scopeConfig->getValue(
                Shipment::XML_PATH_STORE_ADDRESS1, ScopeInterface::SCOPE_STORE
            );
            $street_line2 = $this->scopeConfig->getValue(
                Shipment::XML_PATH_STORE_ADDRESS2, ScopeInterface::SCOPE_STORE
            );

            if ($country) {
                $address[] = $this->localeLists->getCountryTranslation($country);
            }

            if ($state) {
                if (is_numeric($state)) {
                    $region = $this->regionFactory->create()->load($state);
                    $address[] = $region->getName();
                } else {
                    $address[] = $state;
                }
            }

            if ($postcode) {
                $address[] = $postcode;
            }

            if ($city) {
                $address[] = $city;
            }

            if ($street_line1) {
                $address[] = $street_line1;
            }

            if ($street_line2) {
                $address[] = $street_line2;
            }

            return implode('<br/>', $address);

        } else {
            return nl2br($this->scopeConfig->getValue(
                'amrma/shipping/address', ScopeInterface::SCOPE_STORE
            ));
        }
    }

    protected function _getArrayFromConfig($key, $store = null)
    {
        $val = $this->scopeConfig->getValue(
            $key, ScopeInterface::SCOPE_STORE, $store
        );

        if (!is_array($val)) {
            $val = $this->serializer->unserialize($val);
        }

        return array_column($val, 'value');
    }

    public function getResolutions($store = null)
    {
        return $this->_getArrayFromConfig('amrma/properties/resolutions', $store);
    }

    public function getConditions($store = null)
    {
        return $this->_getArrayFromConfig('amrma/properties/conditions', $store);
    }

    public function getReasons($store = null)
    {
        return $this->_getArrayFromConfig('amrma/properties/reasons', $store);
    }

    /**
     * @param $orderId
     *
     * @return integer
     */
    public function getRequestsCount($orderId)
    {
        $collection = $this->objectManager->create(
            '\Amasty\Rma\Model\ResourceModel\Request\Collection'
        );

        $collection->addFilter('order_id', $orderId);

        return $collection->getSize();
    }

    /**
     * @param AbstractCollection $collection
     */
    public function addTimeConditions(AbstractCollection $collection)
    {
        $minAllowedDays = $this->scopeConfig->getValue(
            'amrma/general/min_days', ScopeInterface::SCOPE_STORE
        );

        $maxAllowedDays = $this->scopeConfig->getValue(
            'amrma/general/max_days', ScopeInterface::SCOPE_STORE
        );

        if (!empty($minAllowedDays)) {
            $t = $this->coreDate->timestamp() - (60 * 60 * 24 * $minAllowedDays);

            $collection->addFieldToFilter(
                'main_table.created_at', [
                    'lt' => $this->coreDate->gmtDate('Y-m-d H:i:s', $t),
                ]
            );
        }

        if (!empty($maxAllowedDays)) {
            $t = $this->coreDate->timestamp() - (60 * 60 * 24 * $maxAllowedDays);

            $collection->addFieldToFilter(
                'main_table.created_at', [
                    'gt' => $this->coreDate->gmtDate('Y-m-d H:i:s', $t),
                ]
            );
        }
    }
}
