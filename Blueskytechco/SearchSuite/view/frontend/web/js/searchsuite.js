define([
    'jquery',
    'mage/translate',
    'blueskytechco/choose'
], function ($, $t) { 
    'use strict';
    $.widget('mage.searchSuite', {
        options: {
            typeSearch: ''
        },
        _create: function () {
            var typeSearch = this.options.typeSearch;
            
            if($('.field-by-cat-search').length && $('.field-by-cat-search').hasClass('search-category-dropdown')){
                $('#choose_category').chosen();
            }
            
            $('.block-search .dropdown-toggle .top-search').click(function(event){
                if(typeSearch == 'dropdown'){
                    $('.block-search #search-form').slideToggle();
                }else{
                    if($('.block-search #search-form').hasClass('opend')){
                        $('.block-search #search-form').removeClass('opend');
                        $('html').removeClass('open-search');
                    }else{
                        $('.block-search #search-form').addClass('opend');
                        if($('.search_type_canvas').length || $('.search_type_popup').length){
                            $('html').addClass('open-search');
                        } 
                    }
                }
                if ($(window).width() >= 1200) {
                    $('#search-form .input-text').focus();
                }
                return false;
            });
            $('.block-search .button-close').click(function(event){
                $('.block-search #search-form').removeClass('opend');
                if($('html').hasClass('open-search')){
                    $('html').removeClass('open-search');
                }
                return false;
            });
            $('.field-by-cat-search .cat-item').click(function(){
                $('.field-by-cat-search .cat-item').removeClass('selected');
                $(this).addClass('selected');
                var id = $(this).find('a').data('id');
                $('#choose_category').val(id);
            });
        },
    });
    return $.mage.searchSuite;
});