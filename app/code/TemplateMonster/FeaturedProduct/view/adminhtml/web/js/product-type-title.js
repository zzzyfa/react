/**
 * Copyright © 2017. All rights reserved.
 */

define([
    'jquery',
    'underscore',
    'mage/template',
    'text!TemplateMonster_FeaturedProduct/templates/input-title.html'
], function($, _, mageTemplate, titleTemplate){
    "use strict";

    $.widget('TemplateMonster.productTypeTitle', {

        options: {
            template: titleTemplate
        },
        multiselectBlock: {},
        multiselect: {},
        fieldset: {},

        _create: function() {

            // Check widget for Featured Products instance
            if($('#instance_code').val() != 'featured_products') return false;

            _.bindAll(this, '_setData', '_onChangeTitle', '_generateFields', '_getMap');
            var widget = this;
            this.multiselectBlock = $("[class*='_product_types']", widget.element);
            this.multiselect = $('select.multiselect', this.multiselectBlock);
            this.fieldset = $(this.multiselectBlock).parent('.admin__fieldset');

            this._conditionDesc();
            $('option', this.multiselect).mousedown(function(e, triggered) {
                e.preventDefault();
                if(typeof triggered === 'undefined'){
                    $(this).prop('selected', !$(this).prop('selected'));
                }
                widget._loadData();
                widget._setData();
                widget._onChangeTitle();
                widget._labelsDepend();
                widget._disableCategories();
                return false;
            }).trigger('mousedown', [true]);

            // Label fields dependence on the "Show Label" option
            $("[id$='_show_label']", widget.element).on("change", this._labelsDepend);
        },

        _loadData: function () {
            this._generateFields(this._getMap());
        },

        _setData: function () {
            var data = {};
            var field = '';
            $('.featured_product_field', this.fieldset).each(function (index, element) {
                if(typeof (data[$(element).attr('name')]) == 'undefined'){
                    data[$(element).attr('name')] = {};
                }
                field = $(element).hasClass('data_title')
                    ? 'title'
                    : ($(element).hasClass('data_label')
                        ? 'label'
                        : '');
                data[$(element).attr('name')][field] = $(element).val();
            });
            $('input[id$="_json_data"]', this.fieldset).val(JSON.stringify(data));
        },

        _getData: function () {
            var inputVal = $('input[id$="_json_data"]', this.fieldset).val();
            return inputVal ? $.parseJSON(inputVal) : false;
        },

        _onChangeTitle: function () {
            $('.featured_product_field', this.fieldset).on('change', this._setData);
        },

        _labelsDepend: function () {
            var labelField = $('.type_label', this.fieldset);
            $('option', "select[id$='_show_label']").prop('selected') ? labelField.show() : labelField.hide()
        },

        _getMap: function () {
            var map = {};

            // Get all product types (options within multiselect) and fill array of default values
            $('option', this.multiselect).map(function(){
                map[$(this).val()] = $(this).prop('selected');
                return $(this).val();
            }).get();

            return map;
        },

        _generateFields: function(map){
            var widget = this;
            var data = widget._getData();
            var dataType = '';
            var multiselectBlock = this.multiselectBlock;

            //Check whether the field is available. Add or remove fields.
            $.each(map, function (type, available) {

                if(available) {
                    if(!$('.admin__field.field.'+type).length){
                        dataType = data[type] ? data[type] : false;
                        multiselectBlock.after(widget._getTemplate('option[value='+type+']', dataType, 'title'));
                        $('div.admin__field[class*="_show_label"]', this.fieldset).after(widget._getTemplate('option[value='+type+']', dataType, 'label'));
                    }
                } else {
                    $('.admin__field.field.'+type).remove();
                }
            });
        },

        _disableCategories: function (){
            var categories = $('.admin__field[class*="_categories"]');

            // TODO temporary solution
            var ruleTree = $('.rule-tree-wrapper', categories);
            ruleTree.children().not('ul.rule-param-children').remove();
            ruleTree.html($('ul.rule-param-children', ruleTree));

            var select = $('#conditions__1__new_child', ruleTree);
            $('option:not([value*="category_ids"])', select).remove();
            // endTODO


            // if($('option:selected', this.multiselect).length > 1) {
            //     categories.addClass('disabled');
            //     $('*', categories).bind('click', function(){
            //         return false;
            //     });
            // } else {
            //     categories.removeClass('disabled');
            // }
        },

        _conditionDesc: function () {
            $('.admin__field[class*="_categories"] .rule-tree-wrapper').after(
                '<div class="note admin__field-note">Select category. If choose 2 and more Product Type, categories will be disabled.</div>'
            );
        },

        _getTemplate: function (option, title, field) {
            var template = mageTemplate(this.options.template);
            var data = {};
            var option = $(option, this.multiselect);
            data.title = option.text();
            data.type = option.val();
            data.field = field;
            data.value = title ? title[field] : option.text().replace(' Products', '');
            return template({
                data: data
            });
        }
        
    });

    return $.TemplateMonster.productTypeTitle;

});
