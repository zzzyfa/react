define([
    "jquery",
    "jquery/ui"
], function ($) {
    $.widget('mage.amrmaEdit', {
        options: {
            items: [],
            template: null,
            parent: null,
            defaultItem: null
        },
        rowId: 0,

        _create: function () {
            this.addRow(this.options.defaultItem);
        },
        addRow: function (id, after) {
            var data = this.options.items[id];

            var template = this.options.template.html().replace(/_index_/g, id).replace(/_rowId_/g, this.rowId).trim();
            this.rowId++;
            var row = $($.parseHTML(template));
            row.data('id', id);

            if (after) {
                after.after(row);
            } else {
                this.options.parent.append(row);
            }

            row.find('[data-role=item] option[value=' + id + ']').attr('selected', true);
            row.find('[data-role=remaining_quantity]').html(data.qty);
            row.find('[data-validate*="validate-digits-range"]').addClass('digits-range-0-' + data.qty);
            row.show();

            if (data.type == 'bundle') {
                row.find('[data-role="bundle_items"][data-bundle-id='+id+']').show();
                row.find('[data-role="qty_requested_block"]').hide();
            }

            this.bindEvents(row);
        },
        deleteRow: function (row) {
            row.remove();
        },
        bindEvents: function (row) {
            var widget = this;
            
            row.find('[data-role="item"]').change(function () {
                var id = this.value;

                var after = $(this).parents('[data-role="rma-item"]');
                widget.addRow(id, after);
                widget.deleteRow(after);
            });

            row.find('[data-role="bundle_checkbox"]').click(function () {
                var parentId = $(this).attr('data-bundle-id');
                var item = widget.options.items[parentId].child[this.value];
                var bundle_qty = $(this).parents('tr').find('[data-role="bundle_qty"]');

                if (item.qty > 0) {
                    bundle_qty.removeAttr('disabled');
                } else {
                    bundle_qty.attr('disabled', 'disabled');
                }

                if (this.checked) {
                    bundle_qty.show();
                } else {
                    bundle_qty.hide();
                    bundle_qty.attr('disabled', 'disabled');
                }
            });

            row.find('[data-role="add_item"]').click(function () {
                var id = $(this).parents('[data-role="rma-item"]').data('id');
                widget.addRow(id);
            });

            row.find('[data-role="remove_item"]').click(function () {
                if (widget.options.parent.find('[data-role="rma-item"]').length > 1) {
                    widget.deleteRow($(this).parents('[data-role="rma-item"]'));
                }
            });
        }
    });

    return $.mage.amrmaEdit;
});
