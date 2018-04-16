/**
 * Copyright © 2015. All rights reserved.
 */

define([
    'jquery',
    'featuredOwlCarousel'
], function($){
    "use strict";

    $.widget('TemplateMonster.featuredCarousel', {


        options: {
            items: 3,
            itemsDesktop: [1199,3],
            itemsDesktopSmall: [979,3],
            itemsTablet: [768,2],
            itemsMobile: [400,1],
            autoPlay: false,
            navigation:true,
            pagination: false,
            paginationWrapper: '',
            addClassActive: true,
            navigationText: [],
        },

        _create: function() {
            this.element.owlCarousel(this.options);
        },
        
    });

    return $.TemplateMonster.featuredCarousel;

});
