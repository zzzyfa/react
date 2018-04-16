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
* @package    Bss_MinMaxQtyOrderPerCate
* @author     Extension Team
* @copyright  Copyright (c) 2014-2016 BSS Commerce Co. ( http://bsscommerce.com )
* @license    http://bsscommerce.com/Bss-Commerce-License.txt
*/
 
namespace Bss\MinMaxQtyOrderPerCate\Model\Config\Backend\Serialized;
 
class Minmax extends \Magento\Config\Model\Config\Backend\Serialized\ArraySerialized
{
    public function save()
    {
        $option = array();
        $bssgr = array();
        $option = $this->getValue();
        if (count($option) > 0) {
            foreach ($option as  $qty) {
                if (is_array($qty)) {
                    if (isset($qty['customer_group_id']) && isset($qty['category_id'])) {
                        $bssgr[]= $qty['customer_group_id'].'/'.$qty['category_id'];
                    }
                    if (isset($qty['min_sale_qty']) && isset($qty['max_sale_qty'])) {
                        $offset = $qty['min_sale_qty'] - $qty['max_sale_qty'];
                        if ($offset >= 0) {
                            throw new \Magento\Framework\Exception\LocalizedException(__("Value of Min Qty can't larger  Value of Max Qty"));
                        }
                    }
                }
            }
        }
        
        foreach (array_count_values($bssgr) as $dupcate) {
            if ($dupcate >1 ) {
                throw new \Magento\Framework\Exception\LocalizedException(__("Duplicate  category vs category and groupcustomer vs groupcustomer"));
            }
        }

        return parent::save();
    }
}