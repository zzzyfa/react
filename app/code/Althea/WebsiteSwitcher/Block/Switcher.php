<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Store and language switcher block
 */
namespace Althea\WebsiteSwitcher\Block;

use Magento\Directory\Helper\Data;
use Magento\Store\Model\Group;

class Switcher extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_pageFactory = $pageFactory;
    }

    private function _arrayCopy(array $array)
    {
        $result = array();
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                $result[$key] = $this->_arrayCopy($val);
            } elseif (is_object($val)) {
                $result[$key] = clone $val;
            } else {
                $result[$key] = $val;
            }
        }
        return $result;
    }


    public function getCurrentWebsiteId()
    {
        return $this->_storeManager->getStore()->getWebsiteId();
    }

    public function getFlagUrl($countryName)
    {
        return $countryName . '.svg';
    }

    public function getWebsitesWithoutCurrentWebsite()
    {
        $websites = $this->getWebsites();
        $currentWebsiteId = $this->getCurrentWebsiteId();
        $websitesWithoutCurrentWebsite = [];

        foreach ($websites as $website) {
            if ($website['id'] !== $currentWebsiteId) {
                array_push($websitesWithoutCurrentWebsite, $website);
            }
        }

        return $websitesWithoutCurrentWebsite;
    }

    public function getCurrentWebsite()
    {
        $websites = $this->getWebsites();
        $currentWebsiteId = $this->getCurrentWebsiteId();
        $currentWebsite = null;

        foreach ($websites as $website) {
            if ($website['id'] === $currentWebsiteId && is_null($currentWebsite)) {
                $currentWebsite = $website;
                break;
            }
        }

        return $currentWebsite;
    }

    public function getGlobalWebsite()
    {
        $websites = $this->getWebsites();
        $globalWebsiteCode = 'GLOBAL';
        $globalWebsite = null;

        foreach ($websites as $website) {
            if ($website['code'] === $globalWebsiteCode && is_null($globalWebsite)) {
                $globalWebsite = $website;
                break;
            }
        }

        return $globalWebsite;
    }

    public function getWebsites()
    {
        $_websites = $this->_storeManager->getWebsites();
        $_websiteData = array();

        $_restOfWorld = array();

        foreach ($_websites as $website) {
            $websiteData = array();
            $websiteData['id'] = $website->getId();
            $websiteData['code'] = $website->getCode();

            switch (strtoupper($website->getCode())) {
                case 'MY':
                    $websiteData['name'] = 'MALAYSIA';
                    $websiteData['flag_url'] = $this->getFlagUrl('malaysia');
                    break;

                case 'SG':
                    $websiteData['name'] = 'SINGAPORE';
                    $websiteData['flag_url'] = $this->getFlagUrl('singapore');
                    break;

                case 'PH':
                    $websiteData['name'] = 'PHILIPPINES';
                    $websiteData['flag_url'] = $this->getFlagUrl('philippines');
                    break;

                case 'ID':
                    $websiteData['name'] = 'INDONESIA';
                    $websiteData['flag_url'] = $this->getFlagUrl('indonesia');
                    break;

                case 'TH':
                    $websiteData['name'] = 'THAILAND';
                    $websiteData['flag_url'] = $this->getFlagUrl('thailand');
                    break;

                case 'TW':
                    $websiteData['name'] = 'TAIWAN';
                    $websiteData['flag_url'] = $this->getFlagUrl('taiwan');
                    break;

                case 'US':
                    $websiteData['name'] = 'USA';
                    $websiteData['flag_url'] = $this->getFlagUrl('usa');
                    break;
            }

            foreach ($website->getStores() as $store) {
                //This is for preventing to save mobile url.
                //Because we are using Magento abnormally.
                //TODO::It will be replaced with more efficient way
                if (!array_key_exists('url', $websiteData)) {
                    $storeObj = $this->_storeManager->getStore($store);
                    $websiteData['url'] = $storeObj->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
                }
            }

            array_push($_websiteData, $websiteData);

            //for the rest of the world.
            if (strtoupper($website->getCode()) == 'US') {
                $_restOfWorld = $this->_arrayCopy($websiteData);
                $_restOfWorld['id'] = null;
                $_restOfWorld['code'] = 'GLOBAL';
                $_restOfWorld['name'] = 'REST OF THE WORLD';
                $_restOfWorld['flag_url'] = $this->getFlagUrl('earth');
            }
        }

        array_push($_websiteData, $_restOfWorld);

        return $_websiteData;
    }
}
