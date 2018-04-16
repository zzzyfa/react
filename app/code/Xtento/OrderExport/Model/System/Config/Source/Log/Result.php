<?php

/**
 * Product:       Xtento_OrderExport (2.2.8)
 * ID:            pla2jYgPEb1bu8ncBMlGtw6aKM5PQ018LXPJPkLn5XM=
 * Packaged:      2017-06-01T10:42:36+00:00
 * Last Modified: 2016-03-01T16:15:41+00:00
 * File:          app/code/Xtento/OrderExport/Model/System/Config/Source/Log/Result.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\OrderExport\Model\System\Config\Source\Log;

use Magento\Framework\Option\ArrayInterface;

/**
 * @codeCoverageIgnore
 */
class Result implements ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $values = [
            \Xtento\OrderExport\Model\Log::RESULT_NORESULT => __('No Result'),
            \Xtento\OrderExport\Model\Log::RESULT_SUCCESSFUL => __('Successful'),
            \Xtento\OrderExport\Model\Log::RESULT_WARNING => __('Warning'),
            \Xtento\OrderExport\Model\Log::RESULT_FAILED => __('Failed')
        ];
        return $values;
    }
}
