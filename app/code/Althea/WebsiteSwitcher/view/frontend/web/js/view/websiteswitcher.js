require([
  'jquery'
], function ($) {
  'use strict';
  $(document).ready(function () {
    var switcherWebsite = $('#switcher-website');
    var switcherWebsiteCurrentCountry = $('#switcher-website .current-country');
    var switcherWebsiteCountryList = $('#switcher-website .country-list');

    var openSwitcherWebsite = function () {
      if (!switcherWebsite.hasClass('selected')) {
        switcherWebsite.addClass('selected');
      }
    };

    var closeSwitcherWebsite = function () {
      if (switcherWebsite.hasClass('selected')) {
        switcherWebsite.removeClass('selected');
      }
    };

    switcherWebsiteCurrentCountry.click(function (e) {
        if (!switcherWebsite.hasClass('selected')) {
          openSwitcherWebsite();
        }
        else {
          closeSwitcherWebsite();
        }
    });

    $(document).click(function (e) {
      if (!e.target.matches('.current-country') && switcherWebsite.hasClass('selected')) {
        closeSwitcherWebsite();
      }
    })
  });
});