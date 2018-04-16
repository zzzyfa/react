define([
    'jquery',
    'select2'
], function ($) {

    return function (config, element) {

        var data = config.data;

        $(element).select2({
            data: data
        });
    };
});