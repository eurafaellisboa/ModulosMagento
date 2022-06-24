define([
    "jquery",
	'mage/translate'
], function ($, $t) { 
    'use strict';

    $.widget('mage.widgetMenu', {
        options: {
        },
        _create: function () {
			$(document).on('click', '.widget-menu-list li.ui-menu-item > .open-children-toggle', function(){
				if(!$(this).parent().children(".submenu").hasClass("opened")) {
					$(this).parent().children(".submenu").addClass("opened");
					$(this).parent().children("a").addClass("ui-state-active");
                    $(this).parent().children(".submenu").slideDown(300);
				}
				else {
					$(this).parent().children(".submenu").removeClass("opened");
					$(this).parent().children("a").removeClass("ui-state-active");
                    $(this).parent().children(".submenu").slideUp(300);
				}
			});
			$(document).on('click', '.widget-menu-list .submenu .subchildmenu li.ui-menu-item  > .open-children-toggle', function(){
				if (!$(this).parent().children(".subchildmenu").hasClass("opened")) {
					$(this).parent().children(".subchildmenu").addClass("opened");
					$(this).parent().children("a").addClass("ui-state-active");
					$(this).parent().children(".subchildmenu.opened").slideDown(300);
				} else {
					$(this).parent().children(".subchildmenu").removeClass("opened");
					$(this).parent().children("a").removeClass("ui-state-active");
					$(this).parent().children(".subchildmenu").slideUp(300);
				}
			});
        },
    });
    return $.mage.widgetMenu;
});