<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * BSS Commerce does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BSS Commerce does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   BSS
 * @package    Bss_ShoppingCartRulePerStoreView
 * @author     Extension Team
 * @copyright  Copyright (c) 2016-2017 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\ShoppingCartRulePerStoreView\Observer\SalesRule;

use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\System\Store;

class Save implements ObserverInterface
{
    public $store;

    public function __construct(
        Store $store
    ) {
        $this->store = $store;
    }

    /**
     * Promo quote save action
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $request = $observer->getEvent()->getRequest();
        $data = $request->getPostValue();
       
        $stores = [];
        foreach ($this->store->getStoresStructure(false, $data['store_ids']) as $store_id => $store) {
            $stores[] = $store_id;
        }

        $data['website_ids'] = $stores;
        $request->setPostValue($data);
        return $this;
    }
}
