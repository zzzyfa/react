define([
    "jquery",
    "jquery/ui"
], function ($) {
    $.widget('mage.amLabelPosition', {
        name: null,
        element: null,
        table: null,
        tds: null,
        positionClasses : [
        'top-left','top-center', 'top-right', 'middle-left','middle-center',
        'middle-right', 'bottom-left', 'bottom-center', 'bottom-right'
        ],

        _create: function () {
            this.name = this.element.attr('id').replace('labels_', '');
            this.table = $('#amlabel-table-' + this.name);
            if(!this.table.length || !this.element.length) {
                return;
            }

            var self = this;
            this.tds = this.table.find('td');
            this.table.on( "click", "td", function() {
                self.tdClick(this);
            });

            var currentValue = this.element.val();
            if(currentValue) {
                var td = this.getElementByIndex(parseInt(currentValue));
                $(td).addClass('selected');
            }

        },

        tdClick: function(item){
            var value = this.index(item, 1) - 1;
            if(value >= 0) {
                this.element.val(value);
                this.tds.removeClass('selected');

                $(item).addClass('selected');
            }

            var type = this.element.attr('id').replace('_pos', '');
            var label = $('#' + type + '_preview .amasty-label-container');

            if(label.length) {
                var position = this.positionClasses[this.element.val()];
                label.amShowLabel('setPosition', position);
            }
        },

        getElementByIndex: function(currentValue){
            var col = Math.floor(currentValue/3);
            var cell = currentValue % 3;
            var element = this.table.find('tr:nth-child(' + (col + 1) + ') td:nth-child('+ (cell + 1) + ')')[0];
            return element;
        },

        index: function(node, parent) {
            var index = 0;
            var siblings = node.parentNode.childNodes;
            for (var j in siblings) if (siblings.hasOwnProperty(j)) {
                if (siblings[j].nodeType != Node.ELEMENT_NODE) {
                    continue;
                }
                ++index;
                if (siblings[j] == node) {
                    break;
                }
            }
            if(parent) {
                index += (this.index(node.parentNode, 0) - 1) * 3;
            }
            return index || -1;
        }
    });

    $.widget('mage.amLabelChoose', {
        _create: function () {
            var self = this;
            this.element.on( "click", "input", function() {
                self.itemClick(this);
            });
            $( document ).ready(function() {
                self.element.find('input:checked').click();
            });
        },

        itemClick: function(item){
            var value = item.value;
            if (item.value.indexOf('shape') >= 0) {
                var hide = $('#amlabel-' + value.replace('shape', 'download'));
                var show = $('#amlabel-' + value);
                var type = item.value.replace('shapelabels_', '').replace('_img', '');
                $('.field-' + type + '_label_color').show();
            }
            else{
                var hide = $('#amlabel-' + value.replace('download', 'shape'));
                var show = $('#amlabel-' + value);
                var type = value.replace('downloadlabels_', '').replace('_img', '');
                $('.field-' + type + '_label_color').hide();
            }
            show.show();
            hide.hide();
        }
    });


    $.widget('mage.amLabeltabs', {
        _create: function(){
            $('body').on( "click", 'a.tab-item-link', function() {
                var id = $(this).attr('id');
                $('label_open_tab_input').val(id);
                $.cookie("amasty_labels_current_tab", id);
            });
        }
    });

    $.widget('mage.amLabelPreview', {
        type: '',

        _create: function () {
            var self = this;
            $( document ).ready(function() {
                self._initialize();
                self._initializeControls();
            });
        },

        _initialize: function(item){
            var self = this;
            this.element.hide();
            var menuParent = $('ul.admin__page-nav-items');
            this.element.appendTo(menuParent);
            this.element.wrap('<li>');

            var type = this.element.attr('id').replace('labels_', '').replace('_preview', '');
            if (type == 'prod') {
                var tabId = 'amasty_label_labels_edit_tabs_product_image_section';
            }
            else{
                var tabId = 'amasty_label_labels_edit_tabs_category_image_section';
            }

            var id = $.cookie("amasty_labels_current_tab");
            if (id == tabId) {
                this.element.show();
            }

            menuParent.on( "click", 'a:not(#' + tabId + ')', function() {
                self.element.hide();
            });

            $('#' + tabId).click(function() {
                self.element.show();
            });
        },

        _initializeControls: function() {
            this.type = this.element.attr('id').replace('_preview', '');
            var self = this;
            $('body').on( "blur", 'input[id^="' + this.type + '"], textarea[id^="' + this.type + '"]', function() {
                var element = $(this);
                var value = element.val();
                var name = element.attr('name');
                var label = $('#' + self.type + '_preview .amasty-label-container');
                var type = self.type.replace('labels_', '');
                switch (name) {
                    case (type + '_image_size'):
                        label.css({
                            'width': value + '%'
                        });
                        break;
                    case (type + '_size'):
                        value = value.replace(';', '');
                        label.css({
                            'font-size': value
                        });
                        break;
                    case (type + '_color'):
                        label.css({
                            'color': value
                        });
                        break;
                    case (type + '_style'):
                        var oldStyle = label.attr('style');
                        value = value.split(";");
                        for(item in value) {
                            if(parseInt(item) >= 0) {
                                var st = value[item];
                                var styleName = st.substring(0, st.indexOf(':'));
                                if(styleName) {
                                    if(oldStyle.indexOf(styleName) !== -1) {
                                        var regexp = new RegExp( styleName + "(.*?);");
                                        oldStyle = oldStyle.replace(regexp, st + ';');
                                    }
                                    else{
                                        oldStyle += st + ";";
                                    }
                                }
                            }
                        }
                        label.attr('style', oldStyle);
                        break;
                    case (type + '_txt'):
                        label.find('.amasty-label-text').html(value);
                        break;
                }
            });
        }
    });

});
