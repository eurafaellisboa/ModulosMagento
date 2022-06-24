define([
    "jquery",
	'mage/translate'
], function ($, $t) { 
    'use strict';

    $.widget('mage.horizontalMenu', {
        options: {
        },
        _create: function () {
            var self = this;
            self.addCssSubMenu();
            self.addNextBackMenu();
            $( window ).resize(function() {
                var width = $(window).width();
                if(width >= 1200){
                    self.addNextBackMenu();
                    setTimeout(function() {
                        self.addCssSubMenu();
                    }, 200);
                }
            });
			$('body').on('click', '.nav-toggle,.toggle-nav-footer', function(){
				if(!$("html").hasClass("nav-open")) {
					$("html").addClass("nav-before-open");
					$("html").addClass("nav-open");
				}
				else {
					$("html").removeClass("nav-open");
					$("html").removeClass("nav-before-open");
				}
				return false;
			}); 

            $('body').on('click', '.next-menu-button', function(){
                var navigation = $('.navigation.horizontal');
                var horizontal = $('.navigation .horizontal-list');
                var navigationWidth = navigation.width();
                var horizontalWidth = horizontal.width();
                var translate = navigationWidth-horizontalWidth;
                $('.horizontal-list .custom-static-width > .submenu').attr("style", "");
                horizontal.css("transform","translateX("+translate+"px)");
                $('.next-menu').addClass("slick-disabled");
                $('.back-menu').removeClass("slick-disabled");
                self.addCssSubMenuFullWidth(translate);
                setTimeout(function() {
                    self.addCssSubMenu();
                }, 200);
				return false;
			});

            $('body').on('click', '.back-menu-button', function(){
                var horizontal = $('.navigation .horizontal-list');
                horizontal.css("transform","translateX(0px)");
                $('.back-menu').addClass("slick-disabled");
                $('.next-menu').removeClass("slick-disabled");
                $('.horizontal-list .custom-static-width > .submenu').attr("style", "");
                self.addCssSubMenuFullWidth(0);
                setTimeout(function() {
                    self.addCssSubMenu();
                }, 200);
				return false;
			});
            
			$('body').on('click', '.close-menu', function(){
				if($("html").hasClass("nav-open")) {
					$("html").removeClass("nav-open");
					$("html").removeClass("nav-before-open");
				}
				return false;
			}); 

            $(".next-menu,.back-menu").on("hover", function(e) {
                var horizontal = $('.navigation .horizontal-list');
                if(e.type == "mouseenter") {
                    horizontal.css({"width": ""});
                } else if (e.type == "mouseleave") {
                    horizontal.css({"width": "100%"});
                }
            });

			$('body').on('click', '.horizontal-list li.ui-menu-item > .open-children-toggle', function(){
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
			$('body').on('click', '.horizontal-list .submenu .subchildmenu li.ui-menu-item  > .open-children-toggle', function(){
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
             
            
            var horizontal_list = $(".horizontal-list"); 
            var verticalmenu = $(".navigation .verticalmenu-html");
            
            if(verticalmenu.length){
                $(".navigation.horizontal").append(verticalmenu.html());
                var html_title = '<a data-menu="verticalmenu-list" href="#"><span>'+$t('Categories')+'</span></a>';
                $(".navigation.horizontal .menu-mobile-title").append(html_title);
            }
            
            if(horizontal_list.length){
                $("html").addClass("nav-horizontal");
            }
            
            var header_links = $(".header.links");
            var customer_menu = $(".customer-menu");
            if (customer_menu.length) {
                header_links = $(".customer-menu .header.links");
            }
            setTimeout(function() {
                header_links.children('li').each(function(){
                    if($(this).children('a').length){
                        var html_text = $(this).html();
                        if ($(this).hasClass('wishlist')) {
                            html_text = '<a href="'+$(this).children('a').attr('href')+'">'+$t('My Wish List')+'</a>';
                        } else if ($(this).hasClass('compare')) {
                            html_text = '<a href="'+$(this).children('a').attr('href')+'">'+$t('Compare Products')+'</a>';
                        }
                        var header_links_html = '<li class="ui-menu-item header-links append-content level0">'+html_text+'</li>';
                        horizontal_list.append(header_links_html);
                    }
                });
            }, 2000);
            
            if($(".switcher-language").length){ 
                var language = $(".switcher-language .switcher-options");
                var language_html = '<li class="ui-menu-item switcher-language append-content level0"><a class="switcher-trigger" href="javascript:;">'+language.find('.switcher-trigger').html()+'</a><ul class="switcher-content">'+language.find('.switcher-dropdown').html()+'</ul></li>';
                horizontal_list.append(language_html);
            }
            
            if($(".switcher-currency").length){ 
                var currency = $(".switcher-currency .switcher-options");
                var currency_html = '<li class="ui-menu-item switcher-currency append-content level0"><a class="switcher-trigger" href="javascript:;">'+currency.find('.switcher-trigger').html()+'</a><ul class="switcher-content">'+currency.find('.switcher-dropdown').html()+'</ul></li>';
                horizontal_list.append(currency_html);
            }
            
            $('body').on('click', '.menu-mobile-title a', function(){
                var data = $(this).data('menu');
                $(".menu-mobile-title a").removeClass('active');
                $(this).addClass('active');
                $(".verticalmenu-list,.horizontal-list").hide();
                $("."+data+"").show();
                return false;
            });
            
            $('body').on('click', '.ui-menu-item .switcher-trigger', function(){
                var item = $(this).closest('.ui-menu-item');
                if(item.hasClass('show')){
                    item.removeClass('show');
                    item.find("ul").slideUp(300);
                }else{
                    item.addClass('show');
                    item.find("ul").slideDown(300); 
                }
                return false;
            });
        },
        addCssSubMenu: function () {
            var bodyWidth = $('body').width();
            var header = $('.blueskytechco-header');
            if (!header.length) {
                header = $('header');
            }
            var headerWidth = header.width();
            var headerLeft = header.offset().left;
            var headerRight = bodyWidth - headerWidth - headerLeft;
            $('.horizontal-list .custom-static-width > .submenu').each(function(){
                var elementWidth = $(this).width();
                var elementLeft = $(this).offset().left;
                if ($(this).closest('.custom-static-width').hasClass('dynamic-content')) {
                    if (header.length && header.hasClass('sub-menu-center')) {
                        var left = bodyWidth - (elementWidth + elementLeft);
                        var left_css = $(this).css("left");
                        left_css = parseInt(left_css.replace("px", ""));
                        if (left_css && left_css !== 0) {
                            left = left_css;
                        } else {
                            left = left - (left + elementLeft)/2;
                        }
                        $(this).css("left", left+"px");
                    } else {
                        if (bodyWidth - (elementWidth + elementLeft) < 0) {
                            var left = bodyWidth - (elementWidth + elementLeft);
                            left = left - (left + elementLeft)/2;
                            $(this).css("left", left+"px");
                        }
                    }
                } else {
                    if (bodyWidth - (elementWidth + elementLeft) < 0) {
                        var left = bodyWidth - (elementWidth + elementLeft) - headerRight;
                        if(elementLeft < 0){
                            left = 0;
                        }
                        $(this).css("left", left+"px");
                    }
                }
            });
        },
        addCssSubMenuFullWidth: function (translate) { 
            $('.horizontal-list .fullwidth > .submenu').each(function(){
                $(this).css("left", -translate+"px");
                $(this).css("right", translate+"px");
            });
        },
        addNextBackMenu: function () { 
            var width = $(window).width();
            var navigation = $('.navigation.horizontal');
            var horizontal = $('.navigation .horizontal-list');
            if(width >= 1200){
                if(!navigation.hasClass('next-back-menu-custom')){
                    var navigationWidth = navigation.width();
                    var horizontalWidth = horizontal.width();
                    if(horizontalWidth > navigationWidth){
                        navigation.addClass('next-back-menu-custom');
                        var next_menu = '<div class="next-menu"><a class="next-menu-button slick-next slick-arrow" href="javascript:void(0);">'+$t('Next')+'</a></div>';
                        var back_menu = '<div class="back-menu slick-disabled"><a class="back-menu-button slick-prev slick-arrow" href="javascript:void(0);">'+$t('Back')+'</a></div>';
                        horizontal.before(back_menu);
                        horizontal.after(next_menu);
                        horizontal.css({"transform": "translateX(0px)", "width": "100%"});
                    }else{
                        horizontal.css({"width": "100%"});
                    }
                }
            }else{
                navigation.removeClass('next-back-menu-custom');
                $('.next-menu').remove();
                $('.back-menu').remove();
                horizontal.attr("style", "");
            }
        },
    });
    return $.mage.horizontalMenu;
});