<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rules
 */


use Magento\TestFramework\Helper\Bootstrap;

/** @var \Magento\Framework\Registry $registry */
$registry = Bootstrap::getObjectManager()->get(\Magento\Framework\Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var $order \Magento\Quote\Model\Quote */
$quoteCollection = Bootstrap::getObjectManager()->create(\Magento\Quote\Model\ResourceModel\Quote\Collection::class);
foreach ($quoteCollection as $quote) {
    $quote->delete();
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
