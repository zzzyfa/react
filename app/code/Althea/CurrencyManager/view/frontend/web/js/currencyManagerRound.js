define([
    'round'
], function () {

    return function (config, element) {

        etCurrencyManagerJsConfig = config.data;

        try {

            extendProductConfigformatPrice();
        } catch (e) {

            console.log(e);
        }
    };
});