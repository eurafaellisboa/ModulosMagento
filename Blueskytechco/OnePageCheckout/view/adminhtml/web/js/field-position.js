define(['jquery', 'jquery/ui'], function ($) {
    'use strict';

    $.widget('admin.field_position', {
        _create: function () {
            this.initGrid();
        },

        initGrid: function () {
            var selector = '#' + this.options.blockId + ' ',
                list     = $(selector + '.sortable-list'),
                field, elemWidth;

            var options = {
                tolerance: 'pointer',
                connectWith: '.sortable-list',
                dropOnEmpty: true,
                containment: 'body',
                cancel: '.ui-state-disabled',
                placeholder: 'suggest-position',
                zIndex: 10,
                scroll: false,
                start: function (e, hash) {
                    if (hash.item.hasClass('wide')) {
                        hash.placeholder.addClass('wide');
                    }

                    if (hash.item.hasClass('medium')) {
                        hash.placeholder.addClass('medium');
                    }

                    if (hash.item.hasClass('short')) {
                        hash.placeholder.addClass('short');
                    }
                }
            };

            if (this.options.blockId === 'admin-order-summary') {
                options.items = 'li:not(.ui-state-disabled)';
            }

            list.sortable(options);

            $(selector + '.sortable-list li').disableSelection();
            $(selector + '.sortable-list li').addClass('f-left');

            $(selector + '.containment ul li .attribute-label').resizable({
                maxHeight: 40,
                minHeight: 40,
                zIndex: 10,
                cancel: '.ui-state-disabled',
                helper: 'ui-resizable-border',
                stop: function (e, ui) {
                    field     = ui.element.parent();
                    elemWidth = ui.element.width() / 2;

                    field.removeClass('wide');
                    field.removeClass('medium');
                    field.removeClass('short');

                    if (elemWidth < field.width() * 0.3) {
                        field.addClass('short');
                    } else if (elemWidth > field.width() * 0.6 && elemWidth < field.width() * 0.8) {
                        field.addClass('medium');
                    } else if (elemWidth > field.width() * 0.8) {
                        field.addClass('wide');
                    }

                    ui.element.css({width: ''});
                }
            });
        }
    });

    return $.admin.field_position;
});
