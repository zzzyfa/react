/**
 * althea:
 * - show mini login instead of authentication popup
 */

define([
        'jquery',
        'Magento_Customer/js/customer-data'
    ],
    function ($, customerData) {
        'use strict';

        return function (config, element) {
            $(element).click(function (event) {
                var cart = customerData.get('cart'),
                    customer = customerData.get('customer');

                event.preventDefault();

                if (!customer().firstname && cart().isGuestCheckoutAllowed === false) {

                    var dropdownAccount = $('#account-link');

                    // - scroll to top
                    $('html, body').animate({scrollTop: 0}, 800);

                    // - show mini login popup
                    if (!dropdownAccount.hasClass('selected')) {

                        dropdownAccount.addClass('selected');
                    }

                    return false;
                }
                location.href = config.checkoutUrl;
            });

        };
    }
);
