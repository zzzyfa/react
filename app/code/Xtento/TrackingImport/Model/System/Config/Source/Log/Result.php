<?php

/**
 * Product:       Xtento_TrackingImport (2.1.9)
 * ID:            pla2jYgPEb1bu8ncBMlGtw6aKM5PQ018LXPJPkLn5XM=
 * Packaged:      2017-06-01T10:43:01+00:00
 * Last Modified: 2016-03-05T13:40:03+00:00
 * File:          app/code/Xtento/TrackingImport/Model/System/Config/Source/Log/Result.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Model\System\Config\Source\Log;

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
            \Xtento\TrackingImport\Model\Log::RESULT_NORESULT => __('No Result'),
            \Xtento\TrackingImport\Model\Log::RESULT_SUCCESSFUL => __('Successful'),
            \Xtento\TrackingImport\Model\Log::RESULT_WARNING => __('Warning'),
            \Xtento\TrackingImport\Model\Log::RESULT_FAILED => __('Failed')
        ];
        return $values;
    }
}
