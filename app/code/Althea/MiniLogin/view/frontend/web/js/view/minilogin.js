require([
  'jquery'
], function ($) {
  'use strict';
  $(document).ready(function () {
    var dropdownAccount = $('#account-link');
    var dropdownAccountTitle = $('#account-link .account-tab-title');
    var dropdownAccountContent = $('#account-link .header-top-link-account-link-content');

    var openDropdownAccount = function () {
      if (!dropdownAccount.hasClass('selected')) {
        dropdownAccount.addClass('selected');
      }
    };

    var closeDropdownAccount = function () {
      if (dropdownAccount.hasClass('selected')) {
        dropdownAccount.removeClass('selected');
      }
    };

    dropdownAccountTitle.click(function (e) {
      if (!dropdownAccount.hasClass('selected')) {
        openDropdownAccount();
      }
      else {
        closeDropdownAccount();
      }
    });

    dropdownAccountContent.find('form').click(function (e) {
      e.stopPropagation();
    });

    $(document).click(function (e) {
      if (!e.target.matches('.account-tab-title') && dropdownAccount.hasClass('selected')) {
        closeDropdownAccount();
      }
    })
  });
});