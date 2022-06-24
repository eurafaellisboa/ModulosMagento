define(['jquery'], function ($) {
    'use strict';

    $.widget('admin.field_tabs', {
        _create: function () {
            this.initTabEvent();
            this.initSaveEvent();
        },

        initTabEvent: function () {
            var elem = $('#admin-field-tabs .action-default');
            $('.admin-field-container').show();
            elem.on('click', function () {
                elem.removeClass('_active');
                $(this).addClass('_active');
                return false;
            });
        },

        initSaveEvent: function () {
            var self = this;

            $('.admin-save-position').on('click', function () {
                self.savePosition(self.options.url);
            });
        },

        savePosition: function (url) {
            var self     = this,
                fields   = [],
                oaFields = [],
                field    = {},
                parent   = null;

            $('#position-save-messages').html('');

            $('.sorted-wrapper .sortable-item').each(function (index, el) {
                parent = $(el).parents('.admin-field-container');

                field = {
                    code: $(el).attr('data-code'),
                    colspan: self.getColspan($(el)),
                    required: !!$(el).find('.attribute-required input').is(':checked')
                };

                if ($(el).parents('#admin-address-information').length) {
                    fields.push(field);
                }
            });

            $.ajax({
                method: 'post',
                showLoader: true,
                url: url,
                data: {
                    fields: JSON.stringify(fields)
                },
                success: function (response) {
                    $('#position-save-messages').html(
                        '<div class="message message-' + response.type + ' ' + response.type + ' ">' +
                        '<span>' + response.message + '</span>' +
                        '</div>'
                    );
                }
            });
        },

        getColspan: function (elem) {
            if (elem.hasClass('wide')) {
                return 12;
            } else if (elem.hasClass('medium')) {
                return 9;
            } else if (elem.hasClass('short')) {
                return 3;
            }

            return 6;
        }
    });

    return $.admin.field_tabs;
});
