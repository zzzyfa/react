<?php

namespace TemplateMonster\ThemeOptions\Observer;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Add dynamic styles link.
 *
 * @package TemplateMonster\ThemeOptions\Observer
 */
class AddDynamicStyles implements ObserverInterface
{
    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var array
     */
    protected $_cssOptions = [
        'content_type' => 'link',
        'rel' => 'stylesheet',
        'type' => 'text/css',
        'src_type' => 'url',
        'media' => 'all'
    ];

    /**
     * AddDynamicStyles constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        UrlInterface $urlBuilder)
    {
        $this->_storeManager = $storeManager;
        $this->_urlBuilder = $urlBuilder;
    }

    /**
     * @inheritdoc
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Framework\View\Layout $layout */
        $layout = $observer->getData('layout');

        $pageConfig = $layout->getReaderContext()->getPageConfigStructure();
        $pageConfig->addAssets($this->_getCssUrl(), $this->_cssOptions);
    }

    /**
     * Get CSS url.
     *
     * @return string
     */
    protected function _getCssUrl()
    {
        $store = $this->_storeManager->getStore();
        return $this->_urlBuilder->getUrl('theme_options/css/index', ['_secure' => $store->isCurrentlySecure()]);
    }
}