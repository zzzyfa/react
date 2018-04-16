define(
    [
        'jquery',
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function ($, Component, rendererList) {

        'use strict';

        rendererList.push(
            {
                type     : 'disabled-method',
                component: 'Althea_PaymentFilter/js/view/payment/method-renderer/disabled-method'
            }
        );

        /** Add view logic here if needed */
        return Component.extend({});
    }
);
