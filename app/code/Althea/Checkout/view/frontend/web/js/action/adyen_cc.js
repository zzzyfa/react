define([
        'jquery',
        'mage/url',
        'Magento_Checkout/js/action/select-payment-method',
        'Magento_Checkout/js/checkout-data',
        'Adyen_Payment/js/action/place-order'
    ], function ($, url, selectPaymentMethodAction, checkoutData, placeOrderAction) {

        'use strict';

        const PAYMENT_METHOD = 'adyen_cc';

        return function (config, element) {

            var data           = config.encrypted_data;
            var ccType         = config.cc_type;
            var storeCc        = config.store_cc === '1';
            var errUrl         = config.mcheckout_err_deep_link;
            var generationTime = window.checkoutConfig.payment.adyenCc.generationTime;

            url.setBaseUrl(config.base_url); // set base url explicitly when using empty template
            selectPaymentMethodAction({
                'method'         : PAYMENT_METHOD,
                'po_number'      : null,
                'additional_data': null
            });
            checkoutData.setSelectedPaymentMethod(PAYMENT_METHOD);
            placeOrderAction({
                'method'         : 'adyen_cc',
                'additional_data': {
                    'cc_type'               : ccType,
                    'encrypted_data'        : data,
                    'generationtime'        : generationTime,
                    'store_cc'              : storeCc,
                    'number_of_installments': ''
                }
            }, true).then(function () {}, function (response) {

                var error    = JSON.parse(response.responseText);
                var deepLink = errUrl.replace('%s', error.message);

                if (window.isShowError) {

                    alert(errUrl + "-\n" + error.message);
                }

                window.location.replace(deepLink);
            });
        };
    }
);