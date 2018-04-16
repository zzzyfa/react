define([
        'jquery',
        'mage/url',
        'Magento_Checkout/js/action/select-payment-method',
        'Magento_Checkout/js/action/place-order',
        'Magento_Checkout/js/checkout-data',
        'Magento_Ui/js/model/messages',
        'Magento_Checkout/js/action/redirect-on-success'
    ], function ($, url, selectPaymentMethodAction, placeOrderAction, checkoutData, Messages, redirectOnSuccessAction) {

        'use strict';

        const PAYMENT_METHOD = 'cashondelivery';

        return function (config, element) {

            var errUrl      = config.mcheckout_err_deep_link;
            var paymentData = {
                'method'         : PAYMENT_METHOD,
                'po_number'      : null,
                'additional_data': null
            };

            url.setBaseUrl(config.base_url); // set base url explicitly when using empty template
            selectPaymentMethodAction(paymentData);
            checkoutData.setSelectedPaymentMethod(paymentData);
            placeOrderAction(paymentData, new Messages())
                .fail(function (response) {

                    var error    = JSON.parse(response.responseText);
                    var deepLink = errUrl.replace('%s', error.message);

                    if (window.isShowError) {

                        alert(errUrl + "-\n" + error.message);
                    }

                    window.location.replace(deepLink);
                })
                .done(function () {

                    redirectOnSuccessAction.execute();
                });
        };
    }
);