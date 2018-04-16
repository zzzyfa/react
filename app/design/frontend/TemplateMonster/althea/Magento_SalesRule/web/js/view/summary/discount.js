/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/
define(
    [
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote'
    ],
    function (Component, quote) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magento_SalesRule/summary/discount'
            },
            totals: quote.getTotals(),
            isDisplayed: function() {
                return this.isFullMode() && this.getPureValue() != 0;
            },
            getCouponCode: function() {
                if (!this.totals()) {
                    return null;
                }
                return this.totals()['coupon_code'];
            },
            getPureValue: function() {
                var price = 0;
                if (this.totals() && this.totals().discount_amount) {
                    price = parseFloat(this.totals().discount_amount);
                }
                return price;
            },
            getValue: function() {
                return this.getFormattedPrice(this.getPureValue());
            },
            /**
             * althea:
             * - get discount description instead of coupon code
             */
            getDiscountDescription: function() {

                if (!this.totals() || this.totals()['total_segments'].length < 1) {

                    return null;
                }

                var discountDesc = null;

                this.totals()['total_segments'].forEach(function (elem) {

                    if (elem.code === "discount") {

                        discountDesc = elem.title;
                    }
                });

                return discountDesc;
            }
        });
    }
);
