<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\InventorySuccess\Plugin\CatalogSearch\Model\Search\FilterMapper;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;

if (!class_exists('Magento\CatalogSearch\Model\Search\FilterMapper\StockStatusFilter')) {

    /**
     * Class StockStatusFilter
     * Adds filter by stock status to base select
     */
    class StockStatusFilter
    {
    }
} else {
    class StockStatusFilter extends \Magento\CatalogSearch\Model\Search\FilterMapper\StockStatusFilter
    {
    }
}


