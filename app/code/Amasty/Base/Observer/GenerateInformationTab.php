<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Base
 */


namespace Amasty\Base\Observer;

use Amasty\Base\Helper\Module;
use Magento\Framework\Event\ObserverInterface;

class GenerateInformationTab implements ObserverInterface
{
    const SEO_PARAMS = '?utm_source=extension&utm_medium=backend&utm_campaign=';
    const MAGENTO_VERSION = '_m2';

    private $block;

    /**
     * @var Module
     */
    private $moduleHelper;

    /**
     * @var string
     */
    private $moduleLink;

    /**
     * @var string
     */
    private $moduleCode;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    public function __construct(
        Module $moduleHelper,
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->moduleHelper = $moduleHelper;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $block = $observer->getBlock();
        if ($block) {
            $this->setBlock($block);
            $html = $this->generateHtml();
            $block->setContent($html);
        }
    }

    /**
     * @return string
     */
    private function generateHtml()
    {
        $html = '<div class="amasty-info-block">'
            . $this->showVersionInfo()
            . $this->showUserGuideLink()
            . $this->additionalContent()
            . $this->showModuleExistingConflicts();
        $html .= '</div>';

        return $html;
    }

    /**
     * @return string
     */
    private function additionalContent()
    {
        $html = '';
        if ($content = $this->getBlock()->getAdditionalModuleContent()) {
            $html = '<div class="amasty-additional-content"><span class="message message-warning">'
                . $content
                .'</span></div>';
        }

        return $html;
    }

    /**
     * @return string
     */
    private function showVersionInfo()
    {
        $html = '<div class="amasty-module-version">';

        $currentVer = $this->getCurrentVersion();
        if ($currentVer) {
            $isVersionLast = $this->isLastVersion($currentVer);
            $class = $isVersionLast ? 'last-version' : '';
            $html .= '<div><span class="version-title">'
                . __('Extension version installed: ')
                . '</span>'
                . '<span class="module-version ' . $class . '">' . $currentVer . '</span></div>';

            if (!$isVersionLast) {
                $html .=
                    '<div><span class="upgrade-error message message-warning">'
                    . __(
                        'Update is available and recommended. See the '
                        . '<a target="_blank" href="%1">Change Log</a>',
                        $this->getChangeLogLink()
                    )
                    . '</span></div>';
            }
        }

        $html .= '</div>';

        return $html;
    }

    private function getCurrentVersion()
    {
        $data = $this->moduleHelper->getModuleInfo($this->getModuleCode());

        return isset($data['version']) ? $data['version'] : null;
    }

    private function getModuleCode()
    {
        if (!$this->moduleCode) {
            $this->moduleCode = '';
            $class = get_class($this->getBlock());
            if ($class) {
                $class = explode('\\', $class);
                if (isset($class[0]) && isset($class[1])) {
                    $this->moduleCode = $class[0] . '_' . $class[1];
                }
            }
        }

        return $this->moduleCode;
    }

    /**
     * @return string
     */
    private function getChangeLogLink()
    {
        return $this->getModuleLink()
            . $this->getSeoparams() . 'changelog_' . $this->getModuleCode() . '#changelog';
    }

    /**
     * @return string
     */
    private function showUserGuideLink()
    {
        $html = '<div class="amasty-user-guide"><span class="message success">'
            . __(
                'Confused with configuration?'
                . ' No worries, please consult the <a target="_blank" href="%1">user guide</a>'
                .' to properly configure the extension.',
                $this->getUserGuideLink()
            )
            . '</span></div>';

        return $html;
    }

    private function getUserGuideLink()
    {
        $link = $this->getBlock()->getUserGuide();
        if ($link) {
            $seoLink = $this->getSeoparams();
            if (strpos($link, '?') !== false) {
                $seoLink =str_replace('?', '&', $seoLink);
            }

            $link .= $seoLink . 'userguide_' . $this->getModuleCode();
        }

        return $link;
    }

    /**
     * @return string
     */
    private function getSeoparams()
    {
        return self::SEO_PARAMS;
    }

    /**
     * @param $currentVer
     * @return bool
     */
    private function isLastVersion($currentVer)
    {
        $result = true;
        $allExtensions = $this->moduleHelper->getAllExtensions();
        if ($allExtensions && isset($allExtensions[$this->getModuleCode()])) {
            $module = $allExtensions[$this->getModuleCode()];
            if ($module && is_array($module)) {
                $module = array_shift($module);
            }

            if (isset($module['version']) && $module['version'] > (string)$currentVer) {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * @param $currentVer
     * @return bool
     */
    private function getModuleLink()
    {
        if (!$this->moduleLink) {
            $this->moduleLink = '';
            $allExtensions = $this->moduleHelper->getAllExtensions();
            if ($allExtensions && isset($allExtensions[$this->getModuleCode()])) {
                $module = $allExtensions[$this->getModuleCode()];
                if ($module && is_array($module)) {
                    $module = array_shift($module);
                }

                if (isset($module['url']) && $module['url']) {
                    $this->moduleLink = $module['url'];
                }
            }
        }

        return $this->moduleLink;
    }

    /**
     * @return string
     */
    private function showModuleExistingConflicts()
    {
        $html = '';
        $messages = [];
        foreach ($this->getBlock()->getEnemyExtensions() as $moduleName) {
            if ($this->moduleManager->isEnabled($moduleName)) {
                $messages[] = __(
                    'Incompatibility with the %1. '
                    . 'To avoid the conflicts we strongly recommend turning off the 3rd party mod via the following command: "%2"',
                    $moduleName,
                    'magento module:disable ' . $moduleName
                );
            }
        }

        if (count($messages)) {
            $html = '<div class="amasty-conflicts-title">'
                . __('Problems detected:')
                . '</div>';

            $html .= '<div class="amasty-disable-extensions">';
            foreach ($messages as $message) {
                $html .= '<p class="message message-error">' . $message . '</p>';
            }

            $html .= '</div>';
        }

        return $html;
    }

    /**
     * @return mixed
     */
    public function getBlock()
    {
        return $this->block;
    }

    /**
     * @param mixed $block
     */
    public function setBlock($block)
    {
        $this->block = $block;
    }
}
