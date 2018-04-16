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

namespace Bss\MinMaxQtyOrderPerCate\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    public function getConfig($key, $store = null)
    {
        return $this->scopeConfig->getValue(
            'minmaxqtypercate/bssmmqpc/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getConfigQty($categorie,$customerGroupId)
    {

        $value = $this->getConfig('min_max_qty');
        $value = unserialize($value);
        $result = [];
        $result['max'] = [];
        $result['min'] = [];
        if ($value) {
            foreach ($value as $grty) {
                $ss[] = $grty['customer_group_id'];
                if ($grty['customer_group_id'] == $customerGroupId && $grty['category_id'] ) {

                    $result['max'][$grty['category_id']] = $grty['max_sale_qty'];
                    $result['min'][$grty['category_id']] = $grty['min_sale_qty'];
                }
            }
        }
        return $result;
    }
    public function getMinQty($categorie,$customerGroupId)
    {
        $min = [];
        $result = [];
        if (!empty($min = $this->getConfigQty($categorie,$customerGroupId)['min']) ) {

                foreach ($categorie as $id => $qty) {
                    if ( !empty($min[$id]) ) {
                        $bss_vl[$id] = $qty - $min[$id];
                    }
                }
                if (isset($bss_vl)) {
                    foreach ($bss_vl as $id => $mmqty) {
                    if ($mmqty < 0) {
                            $result[$id] = $min[$id];
                        }
                    }
                }
            return $result;

        }
        return false;
    }
    public function getMaxQty($categorie,$customerGroupId)
    {

        $max = [];
        $result = [];
        if (!empty($max = $this->getConfigQty($categorie,$customerGroupId)['max']) ) {
                foreach ($categorie as $id => $qty) {
                    if ( !empty($max[$id]) ) {
                        $bss_vl[$id] = $max[$id] - $qty;
                    }
                }
                if (isset($bss_vl)) {
                    foreach ($bss_vl as $id => $mxqty) {
                        if ($mxqty < 0) {
                                $result[$id] = $max[$id];
                            }
                    }
                }

            return $result;
        }
        return false;
       
    }

    public function OrderQty($categorie, $customer)
    {
        if ($categorie) {

            $minqty = $this->getMinQty($categorie,$customer);
            $maxqty = $this->getMaxQty($categorie,$customer);
            if (isset($minqty) && $minqty != false) {
                $return['min_qty'] =  $minqty;
            }
            if (isset($maxqty) && $maxqty != false) {
                $return['max_qty'] =  $maxqty;
            }
            if ( isset($return['max_qty']) || isset($return['min_qty']) ) {
                return $return;
            }
            
        }
        return false;
    }
}
