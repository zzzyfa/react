define([
    'jquery',
    'uiRegistry',
    'mage/translate',
], function ($, registry) {
    var amrulesForm = {
        update: function (type) {
            var action = '';
            this.resetFields(type);
            this.renameFieldsToBaseNames();
            var actionFieldset = $('#' + type +'rule_actions_fieldset_').parent();
            var notice = $('[data-index="simple_action"] .admin__field-note');
            var discountStep = $('[data-index="discount_step"]');
            var promoCategories = $('[data-index="amrulesrule[promo_cats]"]');
            var discountQty = $('[data-index="discount_qty"]');
            var applyTo = require('uiRegistry').get("sales_rule_form.sales_rule_form.actions.actions_apply_to.html_content");

            window.amRulesHide = 0;

            actionFieldset.show();
            if (typeof window.amPromoHide !="undefined" && window.amPromoHide == 1) {
                actionFieldset.hide();
            }

            var selector = $('[data-index="simple_action"] select');
            if (type !== 'sales_rule_form') {
                action = selector[1] ? selector[1].value : selector[0].value;
            } else {
                action = selector.val();
            }

            this.checkFieldsValue();
            this.renameRulesSetting(action);
            this.hideElement(notice);
            this.showElement(discountStep);
            this.showElement(discountQty);
            applyTo.visible(true);

            switch (action) {
                case 'groupn':
                    this.hideElement(discountQty);
                    this.showElement(notice);
                    this.showFields(['amrulesrule[skip_rule]', 'amrulesrule[max_discount]'], type);
                    break;
                case 'thecheapest':
                case 'themostexpencive':
                case 'moneyamount':
                case 'aftern_fixed':
                case 'aftern_disc':
                case 'aftern_fixdisc':
                case 'eachn_perc':
                case 'eachn_fixdisc':
                case 'eachn_fixprice':
                case 'groupn_disc':
                    this.showFields(['amrulesrule[skip_rule]', 'amrulesrule[priceselector]', 'amrulesrule[max_discount]'], type);
                    break;
                case 'eachmaftn_perc':
                case 'eachmaftn_fixdisc':
                case 'eachmaftn_fixprice':
                    this.showFields(['amrulesrule[eachm]', 'amrulesrule[skip_rule]', 'amrulesrule[priceselector]', 'amrulesrule[max_discount]'], type);
                    break;
                case 'buyxgetn_perc':
                case 'buyxgetn_fixprice':
                case 'buyxgetn_fixdisc':
                    this.showFields(['amrulesrule[nqty]', 'amrulesrule[skip_rule]', 'amrulesrule[priceselector]', 'amrulesrule[max_discount]'], type);
                    this.showPromoItems();
                    this.showNote();
                    break;
                case 'setof_fixed':
                    this.hideElement(discountStep);
                case 'setof_percent':
                    applyTo.visible(false);
                    actionFieldset.hide();
                    window.amRulesHide = 1;
                    this.showFields(['amrulesrule[max_discount]'], type);
                    this.showPromoItems();
                    this.hideElement(promoCategories);
                    break;
            }

            if (action.indexOf('fixprice') >= 0 || action == 'groupn' || action == 'setof_fixed'){
                this.hideFields(['apply_to_shipping'],type);
            }
        },

        showPromoItems: function () {
            $('[data-index="promo_items"]').show();
        },

        hidePromoItems: function () {
            $('[data-index="promo_items"], [data-index="discount_step"] .admin__field-note').hide();
        },

        showNote: function () {
            $('[data-index="discount_step"] .admin__field-note').show();
        },

        resetFields: function (type) {
            this.showFields([
                'discount_qty', 'discount_step', 'apply_to_shipping', 'simple_free_shipping'
            ], type);
            this.hideFields([
                'amrulesrule[skip_rule]',
                'amrulesrule[max_discount]',
                'amrulesrule[nqty]',
                'amrulesrule[priceselector]',
                'amrulesrule[eachm]'
            ], type);
            this.hidePromoItems();
        },

        hideFields: function (names, type) {
            return this.toggleFields('hide', names, type);
        },

        showFields: function (names, type) {
            return this.toggleFields('show', names, type);
        },

        addPrefix: function (names, type) {
            for (var i = 0; i < names.length; i++) {
                names[i] = type + '.' + type + '.' + 'actions.' + names[i];
            }

            return names;
        },

        toggleFields: function (method, names, type) {
            registry.get(this.addPrefix(names, type), function () {
                for (var i = 0; i < arguments.length; i++) {
                    arguments[i][method]();
                }
            });
        },

        renameRulesSetting: function (action) {
            var discountStep = $('[data-index="discount_step"] label span'),
                discountAmount = $('[data-index="discount_amount"] label span');
            var discountQty = $('[data-index="discount_qty"] label span');

            switch (action) {
                case 'buy_x_get_y':
                    discountStep.text($.mage.__("Buy N Products"));
                    discountAmount.text($.mage.__("Number of Products with Discount"));
                    break;
                case 'eachn_perc':
                case 'eachn_fixdisc':
                case 'eachn_fixprice':
                    discountStep.text($.mage.__("Each N-th"));
                    break;
                case 'eachmaftn_perc':
                case 'eachmaftn_fixdisc':
                case 'eachmaftn_fixprice':
                    discountStep.text($.mage.__("Each Product (step)"));
                    break;
                case 'buyxgetn_perc':
                case 'buyxgetn_fixprice':
                case 'buyxgetn_fixdisc':
                    discountStep.text($.mage.__("Number of X Products"));
                    break;
                case 'setof_fixed':
                    discountQty.text($.mage.__("Max Number of Sets Discount is Applied To"));
                    break;
                default:
                    discountStep.text($.mage.__("Discount Qty Step (Buy X)"));
                    discountAmount.text($.mage.__("Discount Amount"));
                    break;
            }
        },

        showElement: function (name) {
            name.show();
        },

        hideElement: function (name) {
            name.hide();
        },

        renameFieldsToBaseNames:function ()
        {
            var discountQty = $('[data-index="discount_qty"] label span');
            discountQty.text($.mage.__("Maximum Qty Discount is Applied To"));
        },

        checkFieldsValue:function () {
            var discountQty = require('uiRegistry').get('sales_rule_form.sales_rule_form.actions.discount_qty');
            var discountStep = require('uiRegistry').get('sales_rule_form.sales_rule_form.actions.discount_step');

            if(discountQty.value() < 0) {
                discountQty.value(0);
            }

            if(discountStep.value() == 0 || discountStep.value() == '') {
                discountStep.value(1);
            }
        }
    };

    return amrulesForm;
});