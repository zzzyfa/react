<?php

/**
 * Product:       Xtento_XtCore (2.0.7)
 * ID:            pla2jYgPEb1bu8ncBMlGtw6aKM5PQ018LXPJPkLn5XM=
 * Packaged:      2017-06-01T10:43:07+00:00
 * Last Modified: 2016-05-30T13:06:44+00:00
 * File:          app/code/Xtento/XtCore/Helper/AbstractModule.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\XtCore\Helper;

abstract class AbstractModule extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $edition;
    protected $extId;
    protected $configPath;
    protected $module;

    /**
     * Registry
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Xtento\XtCore\Helper\Server
     */
    protected $serverHelper;

    /**
     * @var Utils
     */
    protected $utilsHelper;

    /**
     * AbstractModule constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param Server $serverHelper
     * @param Utils $utilsHelper
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Registry $registry,
        \Xtento\XtCore\Helper\Server $serverHelper,
        \Xtento\XtCore\Helper\Utils $utilsHelper
    ) {
        $this->registry = $registry;
        $this->serverHelper = $serverHelper;
        $this->utilsHelper = $utilsHelper;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function isModuleEnabled()
    {
        if (!$this->scopeConfig->isSetFlag($this->getConfigPath() . 'enabled')) {
            return false;
        }
        $moduleEnabled = $this->scopeConfig->getValue($this->getConfigPath() . str_rot13('frevny'));
        if (empty($moduleEnabled) || !$moduleEnabled || (0x28 !== strlen(trim($moduleEnabled)))) {
            return false;
        }
        $this->registry->register('xtDisabled', false, true);
        return true;
    }

    /**
     * @param $updateConfiguration
     *
     * @return bool
     */
    public function confirmEnabled($updateConfiguration = false)
    {
        return $this->serverHelper->confirm(
            [
                'module' => $this->getModule(),
                'ext_id' => $this->getExtId(),
                'config_path' => $this->getConfigPath()
            ],
            $updateConfiguration
        );
    }

    public function getExtId()
    {
        return $this->extId;
    }

    public function getConfigPath()
    {
        return $this->configPath;
    }

    public function getModule()
    {
        return $this->module;
    }

    public function getModuleEdition()
    {
        return $this->edition;
    }

    /**
     * Check if module is installed in wrong Magento edition
     *
     * @return bool
     */
    public function isWrongEdition()
    {
        $versionString = 'version';
        if ($this->getModuleEdition() !== '%!'.$versionString.'!%' && $this->getModuleEdition() !== '') {
            if ($this->utilsHelper->isMagentoEnterprise() && $this->getModuleEdition() !== 'EE') {
                return true;
            }
        }
        return false;
    }
}
