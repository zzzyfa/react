define([
        'jquery',
        'molpayseamlessdeco',
        'Magento_Checkout/js/action/select-payment-method',
        'Magento_Checkout/js/checkout-data'
    ], function ($, ms, selectPaymentMethodAction, checkoutData) {

        'use strict';

        const PAYMENT_METHOD = 'molpay_seamless';

        return function (config, element) {

            $('#seamless-form-submit').on('click', function(e) {

                e.preventDefault();
                selectPaymentMethodAction({
                    'method'         : PAYMENT_METHOD,
                    'po_number'      : null,
                    'additional_data': null
                });
                checkoutData.setSelectedPaymentMethod(PAYMENT_METHOD);

                var myForm = $("#seamless");

                if (myForm[0].checkValidity()) {

                    myForm.trigger("submit");
                }
            });
        };
    }
);