<?php

/**
 * Product:       Xtento_OrderExport (2.2.8)
 * ID:            pla2jYgPEb1bu8ncBMlGtw6aKM5PQ018LXPJPkLn5XM=
 * Packaged:      2017-06-01T10:42:37+00:00
 * Last Modified: 2016-02-29T15:46:26+00:00
 * File:          app/code/Xtento/OrderExport/Model/Export/Condition/Custom.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\OrderExport\Model\Export\Condition;

class Custom extends \Magento\Framework\Model\AbstractModel
{
    protected $customAttributes = [];
    protected $customNotMappedAttributes = [];
    protected $omitAttributes = [];

    public function getCustomAttributes()
    {
        // Omitted attributes
        $this->omitAttributes = [
            #'status' => 'Status',
            #'state' => 'State',
        ];
        // Custom ready to use attributes
        $attributes = [
            'payment_method' => __('Payment Method'),
            'shipping_method' => __('Shipping Method'),
            'xt_billing_postcode' => __('Billing Postcode'),
            'xt_billing_region' => __('Billing Region'),
            'xt_billing_region_id' => __('Billing State/Province'),
            'xt_billing_country_id' => __('Billing Country'),
            'xt_shipping_postcode' => __('Shipping Postcode'),
            'xt_shipping_region' => __('Shipping Region'),
            'xt_shipping_region_id' => __('Shipping State/Province'),
            'xt_shipping_country_id' => __('Shipping Country'),
        ];
        $this->customAttributes = $attributes;
        return $attributes;
    }

    /*
     * Further attributes from this entity
     */
    public function getCustomNotMappedAttributes($type = '')
    {
        if (empty($this->customAttributes)) {
            $this->customAttributes = $this->getCustomAttributes();
        }
        if (!empty($this->customNotMappedAttributes)) {
            return $this->customNotMappedAttributes;
        }
        $entity = $this->_registry->registry('orderexport_profile')->getEntity();
        $resource = $this->_registry->registry('orderexport_profile')->getResource();
        $columns = array_keys($resource->getConnection()->describeTable($resource->getTable('sales_' . $entity . $type)));
        sort($columns);

        $attributes = [];
        foreach ($columns as $column) {
            if (isset($this->customAttributes[$column]) || isset($this->omitAttributes[$column])) {
                continue;
            }
            $attributes[$column] = $column;
        }
        $this->customNotMappedAttributes = $attributes;
        return $attributes;
    }
}