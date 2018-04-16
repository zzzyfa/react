/**
 * This script is a simple wrapper that allows you to use OwlCarousel with Magento 2
 */

define([
    "jquery",
    "Vmasciotta_OwlCarousel/js/owl.carousel2",
    "domReady!"
], function($){
    return function(config, element) {
        $(element).owlCarousel2(config);
    }
});