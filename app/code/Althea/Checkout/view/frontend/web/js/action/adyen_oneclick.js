define([
        'jquery',
        'mage/url',
        'Magento_Checkout/js/action/select-payment-method',
        'Magento_Checkout/js/checkout-data',
        'Adyen_Payment/js/action/place-order'
    ], function ($, url, selectPaymentMethodAction, checkoutData, placeOrderAction) {

        'use strict';

        const PAYMENT_METHOD = 'adyen_oneclick';

        return function (config, element) {

            var data        = config.encrypted_data;
            var referenceId = config.reference_id;
            var variant     = config.variant;
            var errUrl      = config.mcheckout_err_deep_link;

            url.setBaseUrl(config.base_url); // set base url explicitly when using empty template
            selectPaymentMethodAction({
                'method'         : PAYMENT_METHOD,
                'po_number'      : null,
                'additional_data': {
                    'recurring_detail_reference': referenceId
                }
            });
            checkoutData.setSelectedPaymentMethod(PAYMENT_METHOD);
            placeOrderAction({
                'method'         : 'adyen_oneclick',
                'po_number'      : null,
                'additional_data': {
                    'encrypted_data'            : data,
                    'recurring_detail_reference': referenceId,
                    'variant'                   : variant,
                    'number_of_installments'    : ''
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