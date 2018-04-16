<?php

/**
 * Product:       Xtento_XtCore (2.0.7)
 * ID:            pla2jYgPEb1bu8ncBMlGtw6aKM5PQ018LXPJPkLn5XM=
 * Packaged:      2017-06-01T10:43:07+00:00
 * Last Modified: 2016-03-02T13:16:13+00:00
 * File:          app/code/Xtento/XtCore/Cron/RegisterCronExecution.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\XtCore\Cron;

class RegisterCronExecution
{
    /**
     * @var \Xtento\XtCore\Model\ResourceModel\Config
     */
    protected $xtCoreConfig;

    /**
     * RegisterCronExecution constructor.
     * @param \Xtento\XtCore\Model\ResourceModel\Config $xtCoreConfig
     */
    public function __construct(
        \Xtento\XtCore\Model\ResourceModel\Config $xtCoreConfig
    ) {
        $this->xtCoreConfig = $xtCoreConfig;
    }

    /**
     * Register last cronjob execution
     *
     * @return void
     */
    public function execute()
    {
        $this->xtCoreConfig->saveConfig('xtcore/crontest/last_execution', time());
    }
}
