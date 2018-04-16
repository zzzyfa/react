<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rgrid
 */

namespace Amasty\Rgrid\Model;

use Amasty\Rules\Helper\Data;
use Magento\Framework\Option\ArrayInterface;

class RuleActions implements ArrayInterface
{
    private $moduleManager;

    public function __construct(\Magento\Framework\Module\Manager $moduleManager)
    {
        $this->moduleManager = $moduleManager;
    }

    public function toOptionArray()
    {
        $options = [
            'by_percent'  => __('Percent of product price discount'),
            'by_fixed'    => __('Fixed amount discount'),
            'cart_fixed'  => __('Fixed amount discount for whole cart'),
            'buy_x_get_y' => __('Buy N products, and get next products with discount')
        ];

        if ($this->moduleManager->isEnabled('Amasty_Promo')) {
            $amastyPromoOptions = [
                'ampromo_items'  => __('Auto add promo items with products'),
                'ampromo_cart'    => __('Auto add promo items for the whole cart'),
                'ampromo_product'  => __('Auto add the same product'),
                'ampromo_spent' => __('Auto add promo items for every $X spent')
            ];

            $options = array_merge($options, $amastyPromoOptions);
        }

        if ($this->moduleManager->isEnabled('Amasty_Rules')) {
            $amastyRulesOptions = [
                Data::TYPE_CHEAPEST => __('The Cheapest, also for Buy 1 get 1 free'),
                Data::TYPE_EXPENCIVE => __('The Most Expensive'),
                Data::TYPE_AMOUNT => __('Get $Y for each $X spent'),

                Data::TYPE_EACH_N => __('Percent Discount: each 2-d, 4-th, 6-th with 15% 0ff'),
                Data::TYPE_EACH_N_FIXDISC => __('Fixed Discount: each 3-d, 6-th, 9-th with $15 0ff'),
                Data::TYPE_EACH_N_FIXED => __('Fixed Price: each 5th, 10th, 15th for $49'),

                Data::TYPE_EACH_M_AFT_N_PERC =>
                    __('Percent Discount: each 1st, 3rd, 5th with 15% 0ff after 5 items added to the cart'),
                Data::TYPE_EACH_M_AFT_N_DISC =>
                    __('Fixed Discount: each 3d, 7th, 11th with $15 0ff after 5 items added to the cart'),
                Data::TYPE_EACH_M_AFT_N_FIX =>
                    __('Fixed Price: each 5th, 7th, 9th for $89.99 after 5 items added to the cart'),

                Data::TYPE_GROUP_N => __('Fixed Price: Each 5 items for $50'),
                Data::TYPE_GROUP_N_DISC => __('Percent Discount: Each 5 items with 10% off'),

                Data::TYPE_XN_PERCENT => __('Percent Discount: Buy X get Y Free'),
                Data::TYPE_XN_FIXDISC => __('Fixed Discount:  Buy Y get Y with $10 Off'),
                Data::TYPE_XN_FIXED => __('Fixed Price: Buy X get Y for $9.99'),

                Data::TYPE_AFTER_N_DISC => __('Percent Discount'),
                Data::TYPE_AFTER_N_FIXDISC => __('Fixed Discount'),
                Data::TYPE_AFTER_N_FIXED => __('Fixed Price'),

                Data::TYPE_SETOF_PERCENT => __('Percent discount for product set'),
                Data::TYPE_SETOF_FIXED => __('Fixed price for product set')
            ];

            $options = array_merge($options, $amastyRulesOptions);
        }

        return $options;
    }
}
