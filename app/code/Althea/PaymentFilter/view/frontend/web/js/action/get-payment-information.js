define(
    [
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/url-builder',
        'mage/storage',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/payment/method-converter',
        'Magento_Checkout/js/model/payment-service',
        'underscore'
    ],
    function ($, quote, urlBuilder, storage, errorProcessor, customer, methodConverter, paymentService, _) {
        'use strict';

        return function (deferred, messageContainer) {
            var serviceUrl;

            deferred = deferred || $.Deferred();
            /**
             * Checkout for guest and registered customer.
             */
            if (!customer.isLoggedIn()) {
                serviceUrl = urlBuilder.createUrl('/guest-carts/:cartId/payment-information', {
                    cartId: quote.getQuoteId()
                });
            } else {
                serviceUrl = urlBuilder.createUrl('/carts/mine/payment-information', {});
            }

            return storage.get(
                serviceUrl, false
            ).done(
                function (response) {
                    quote.setTotals(response.totals);

                    // althea: include disabled payment methods for rendering
                    var methods = [];

                    _.each(response.payment_methods, function (elem) {

                        elem.disabled = false; // althea: set enabled flag (for rendering only)

                        methods.push(elem);
                    });

                    if (response.hasOwnProperty('extension_attributes') && response.extension_attributes.hasOwnProperty('disabled_payment_methods')) {

                        _.each(response.extension_attributes.disabled_payment_methods, function (elem) {

                            elem.disabled = true; // althea: set disabled flag (for rendering only)

                            methods.push(elem);
                        });
                    }

                    paymentService.setPaymentMethods(methodConverter(methods));

                    deferred.resolve();
                }
            ).fail(
                function (response) {
                    errorProcessor.process(response, messageContainer);
                    deferred.reject();
                }
            );
        };
    }
);