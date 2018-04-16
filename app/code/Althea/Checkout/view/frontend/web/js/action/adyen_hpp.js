define([
        'jquery',
        'mage/url',
        'Magento_Checkout/js/action/select-payment-method',
        'Magento_Checkout/js/checkout-data',
        'Adyen_Payment/js/action/set-payment-method'
    ], function ($, url, selectPaymentMethodAction, checkoutData, setPaymentMethodAction) {

        'use strict';

        const PAYMENT_METHOD = 'adyen_hpp';

        return function (config, element) {

            url.setBaseUrl(config.base_url); // set base url explicitly when using empty template

            var brandCode = config.brand_code;
            var errUrl    = config.mcheckout_err_deep_link;

            selectPaymentMethodAction({
                'method'         : PAYMENT_METHOD,
                'po_number'      : null,
                'additional_data': {
                    'brand_code': brandCode
                }
            });
            checkoutData.setSelectedPaymentMethod(PAYMENT_METHOD);
            setPaymentMethodAction().then(function () {}, function (response) {

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