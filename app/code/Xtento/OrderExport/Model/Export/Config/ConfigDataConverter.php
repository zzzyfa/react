<?php

/**
 * Product:       Xtento_OrderExport (2.2.8)
 * ID:            pla2jYgPEb1bu8ncBMlGtw6aKM5PQ018LXPJPkLn5XM=
 * Packaged:      2017-06-01T10:42:37+00:00
 * Last Modified: 2017-03-06T13:10:13+00:00
 * File:          app/code/Xtento/OrderExport/Model/Export/Config/ConfigDataConverter.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\OrderExport\Model\Export\Config;

class ConfigDataConverter implements \Magento\Framework\Config\ConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function convert($source)
    {
        $classes = [];
        foreach ($source->getElementsByTagName('export') as $exportClass) {
            $id = $exportClass->getAttribute('id');
            $classes[$id] = [
                'class' => $exportClass->getAttribute('class'),
                'profile_ids' => !empty($exportClass->getAttribute('profile_ids')) ? $exportClass->getAttribute('profile_ids') : false
            ];
        }
        return [
            'classes' => $classes,
        ];
    }
}
