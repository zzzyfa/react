<?php

/**
 * Product:       Xtento_TrackingImport (2.1.9)
 * ID:            pla2jYgPEb1bu8ncBMlGtw6aKM5PQ018LXPJPkLn5XM=
 * Packaged:      2017-06-01T10:43:01+00:00
 * Last Modified: 2016-03-13T19:37:15+00:00
 * File:          app/code/Xtento/TrackingImport/Model/Source/SourceInterface.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Model\Source;

interface SourceInterface
{
    public function testConnection();

    public function loadFiles();

    public function archiveFiles($filesToProcess, $forceDelete = false);
}