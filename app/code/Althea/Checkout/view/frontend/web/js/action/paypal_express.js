define([
        'jquery',
        'Magento_Checkout/js/action/select-payment-method',
        'Magento_Checkout/js/checkout-data',
        'Magento_Paypal/js/action/set-payment-method',
        'Magento_Checkout/js/model/quote',
        'Magento_Customer/js/customer-data',
        'Magento_Ui/js/model/messages'
    ], function ($, selectPaymentMethodAction, checkoutData, setPaymentMethodAction, quote, customerData, Messages) {

        'use strict';

        const PAYMENT_METHOD = 'paypal_express';

        return function (config, element) {

            selectPaymentMethodAction({
                'method'         : PAYMENT_METHOD,
                'po_number'      : null,
                'additional_data': null
            });
            checkoutData.setSelectedPaymentMethod(PAYMENT_METHOD);
            setPaymentMethodAction(new Messages()).done(function () {

                customerData.invalidate(['cart']);

                $.mage.redirect(
                    window.checkoutConfig.payment.paypalExpress.redirectUrl[quote.paymentMethod().method]
                );
            });
        };
    }
);