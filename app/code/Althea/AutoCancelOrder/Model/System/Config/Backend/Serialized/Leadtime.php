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

namespace Althea\AutoCancelOrder\Model\System\Config\Backend\Serialized;

class Leadtime extends \Magento\Config\Model\Config\Backend\Serialized\ArraySerialized
{
    public function save()
    {
        $option = array();
        $bssgr = array();
        $option = $this->getValue();
        if (count($option) > 0) {
            foreach ($option as  $payment) {
                if (is_array($payment)) {
                    if (isset($payment['payment_id'])) {
                        $bssgr[]= $payment['payment_id'];
                    }
                    if (isset($payment['schedule'])) {
                        $offset = $payment['schedule'];
                    }
                }
            }
        }

        foreach (array_count_values($bssgr) as $dupcate) {
            if ($dupcate >1 ) {
                throw new \Magento\Framework\Exception\LocalizedException(__("Duplicate payment"));
            }
        }

        return parent::save();
    }
}