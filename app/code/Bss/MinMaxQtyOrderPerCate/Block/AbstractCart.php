<?php
namespace Bss\MinMaxQtyOrderPerCate\Block;

class AbstractCart
{
    public function afterGetItemRenderer(\Magento\Checkout\Block\Cart\AbstractCart $subject, $result)
    {
    	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$_helper = $objectManager->get('\Bss\MinMaxQtyOrderPerCate\Helper\Data');
		if ($_helper->getConfig('show_category')) {
			$result->setTemplate('Bss_MinMaxQtyOrderPerCate::cart/item/default.phtml');
		}
    	return $result;
    }
}
