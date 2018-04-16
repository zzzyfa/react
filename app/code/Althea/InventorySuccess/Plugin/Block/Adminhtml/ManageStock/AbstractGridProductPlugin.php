<?php

/**
 * Created by PhpStorm.
 * User: Jungho Park
 * Date: 01/15/2018
 * Time: 4:00 PM
 */

namespace Althea\InventorySuccess\Plugin\Block\Adminhtml\ManageStock;

class AbstractGridProductPlugin
{
    public function aroundModifyColumns(\Magestore\InventorySuccess\Block\Adminhtml\ManageStock\AbstractGridProduct $items, \Closure $proceed)
    {
        $result = $proceed();

        if (!$result) {

        	return $result;
        }

        $result->addColumnAfter("pending_qty",
            [
                "header" => __("Pending Qty"),
                "index" => "pending_qty",
                'type' => 'number',
                "sortable" => true,
                'filter_condition_callback' => array($result, '_filterTotalQtyCallback')
            ]
            ,"available_qty"
        );

        $result->addColumnAfter("processing_qty",
            [
                "header" => __("Processing Qty "),
                "index" => "processing_qty",
                'type' => 'number',
                "sortable" => true,
                'filter_condition_callback' => array($result, '_filterTotalQtyCallback')
            ]
            ,"pending_qty"
        );

        $result->addColumnAfter("canceled_qty",
            [
                "header" => __("Canceled Qty"),
                "index" => "canceled_qty",
                'type' => 'number',
                "sortable" => true,
                'filter_condition_callback' => array($result, '_filterTotalQtyCallback')
            ]
            ,"processing_qty"
        );

        return $result;
    }
}