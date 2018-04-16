<?php

/**
 * Product:       Xtento_OrderExport (2.2.8)
 * ID:            pla2jYgPEb1bu8ncBMlGtw6aKM5PQ018LXPJPkLn5XM=
 * Packaged:      2017-06-01T10:42:36+00:00
 * Last Modified: 2015-08-10T10:20:49+00:00
 * File:          app/code/Xtento/OrderExport/Model/Destination/DestinationInterface.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\OrderExport\Model\Destination;

interface DestinationInterface
{
    public function testConnection();
    public function saveFiles($fileArray);
}