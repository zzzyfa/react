<?php

/**
 * Product:       Xtento_TrackingImport (2.1.9)
 * ID:            pla2jYgPEb1bu8ncBMlGtw6aKM5PQ018LXPJPkLn5XM=
 * Packaged:      2017-06-01T10:43:01+00:00
 * Last Modified: 2016-04-11T12:58:55+00:00
 * File:          app/code/Xtento/TrackingImport/Block/Adminhtml/Profile/Edit/Tab/Mapping/Mapper.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Block\Adminhtml\Profile\Edit\Tab\Mapping;

class Mapper extends AbstractMapping
{
    public $mappingId = 'mapping';
    public $mappingModel = 'Xtento\TrackingImport\Model\Processor\Mapping\Fields';
    public $fieldLabel = 'Magento Field';
    public $valueFieldLabel = 'File Field Name / Index';
    public $hasDefaultValueColumn = true;
    public $hasValueColumn = true;
    public $defaultValueFieldLabel = 'Default Value';
    public $addFieldLabel = 'Add field to mapping';
    public $addAllFieldLabel = 'Add all fields';
    public $selectLabel = '--- Select field ---';
}
