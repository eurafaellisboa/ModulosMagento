define([
        "jquery",
        "jquery/ui",
        "Magento_Theme/js/view/messages",
        "ko",
        "Magento_Catalog/js/product/list/toolbar"

    ], function ($, ui, messageComponent, ko) {
        $.widget('mage.productListToolbarForm', $.mage.productListToolbarForm, {

            options: {
                modeControl: '[data-role="mode-switcher"]',
                directionControl: '[data-role="direction-switcher"]',
                orderControl: '[data-role="sorter"]',
                limitControl: '[data-role="limiter"]',
                mode: 'product_list_mode',
                direction: 'product_list_dir',
                order: 'product_list_order',
                limit: 'product_list_limit',
                modeDefault: 'grid',
                directionDefault: 'asc',
                orderDefault: 'position',
                limitDefault: '9',
                url: '',
                formKey: '',
                post: false,
                pagerControl: '[data-role="pager"], .pages-items a',
                pager: 'p',
                pagerDefault: '1',
                productsToolbarControl:'.toolbar.toolbar-products',
                productsListBlock: '.products.wrapper',
                layeredNavigationFilterBlock: '.block.filter',
                filterItemControl: '.block.filter .item a, .block.filter .filter-clear,.block.filter .swatch-option-link-layered, .pages-items a'
            },

            _create: function () {
                if (window.checkout.isSparshALNEnable == 1) {
                    this._super();
                    $(this.options.filterItemControl)
                            .off('click.' + this.namespace + 'productListToolbarForm')
                            .on('click.' + this.namespace + 'productListToolbarForm', {}, $.proxy(this.applyFilterToProductsList, this))
                            ;
                } else {
                    this._bind($(this.options.modeControl), this.options.mode, this.options.modeDefault);
                    this._bind($(this.options.directionControl), this.options.direction, this.options.directionDefault);
                    this._bind($(this.options.orderControl), this.options.order, this.options.orderDefault);
                    this._bind($(this.options.limitControl), this.options.limit, this.options.limitDefault);
                }
            },

            /** @inheritdoc */
            _bind: function (element, paramName, defaultValue) {
                if (element.is('select')) {
                    element.on('change', {
                        paramName: paramName,
                        'default': defaultValue
                    }, $.proxy(this._processSelect, this));
                } else {
                    element.on('click', {
                        paramName: paramName,
                        'default': defaultValue
                    }, $.proxy(this._processLink, this));
                }
            },

            applyFilterToProductsList: function (evt) {
                var link = $(evt.currentTarget);
                var urlParts = link.attr('href').split('?');
                this.makeAjaxCall(urlParts[0], urlParts[1]);
                evt.preventDefault();
            },
            updateUrl: function (url, paramData) {
                if (!url) {
                    return;
                }
                if (paramData && paramData.length > 0) {
                    url += '?' + paramData;
                }

                if (typeof history.replaceState === 'function') {
                    history.replaceState(null, null, url);
                }
            },

            getParams: function (urlParams, paramName, paramValue, defaultValue) {
                var paramData = {},
                        parameters;

                for (var i = 0; i < urlParams.length; i++) {
                    parameters = urlParams[i].split('=');
                    if (parameters[1] !== undefined) {
                        paramData[parameters[0]] = parameters[1];
                    } else {
                        paramData[parameters[0]] = '';
                    }
                }

                paramData[paramName] = paramValue;
                if (paramValue == defaultValue) {
                    delete paramData[paramName];
                }
                return window.decodeURIComponent($.param(paramData).replace(/\+/g, '%20'));
            },
            _updateContent: function (content) {
                $(this.options.productsToolbarControl).remove();
                if (content.products_list) {
                    if ($(this.options.productsListBlock).length > 1) {
                        $(this.options.productsListBlock).not(':last').remove();
                    }
                    $(this.options.productsListBlock)
                            .replaceWith(content.products_list)
                            ;
                }

                if (content.filters) {
                    $(this.options.layeredNavigationFilterBlock).replaceWith(content.filters)
                }

                $('body').trigger('contentUpdated');
            },

            updateContent: function (content) {
                $('html, body').animate(
                        {
                            scrollTop: $(this.options.productsToolbarControl + ":first").offset().top
                        },
                        100,
                        'swing',
                        this._updateContent(content)
                        );
            },

            changeUrl: function (paramName, paramValue, defaultValue) {
                if (window.checkout.isSparshALNEnable == 1) {
                    var urlPaths = this.options.url.split('?'),
                            baseUrl = urlPaths[0],
                            urlParams = urlPaths[1] ? urlPaths[1].split('&') : [],
                            paramData = this.getParams(urlParams, paramName, paramValue, defaultValue);

                    this.makeAjaxCall(baseUrl, paramData);
                } else {
                    var decode = window.decodeURIComponent,
                            urlPaths = this.options.url.split('?'),
                            baseUrl = urlPaths[0],
                            urlParams = urlPaths[1] ? urlPaths[1].split('&') : [],
                            paramData = {},
                            parameters, i, form, params, key, input, formKey;

                    for (i = 0; i < urlParams.length; i++) {
                        parameters = urlParams[i].split('=');
                        paramData[decode(parameters[0])] = parameters[1] !== undefined ?
                                decode(parameters[1].replace(/\+/g, '%20')) :
                                '';
                    }
                    paramData[paramName] = paramValue;

                    if (this.options.post) {
                        form = document.createElement('form');
                        params = [this.options.mode, this.options.direction, this.options.order, this.options.limit];

                        for (key in paramData) {
                            if (params.indexOf(key) !== -1) {
                                input = document.createElement('input');
                                input.name = key;
                                input.value = paramData[key];
                                form.appendChild(input);
                                delete paramData[key];
                            }
                        }
                        formKey = document.createElement('input');
                        formKey.name = 'form_key';
                        formKey.value = this.options.formKey;
                        form.appendChild(formKey);

                        paramData = $.param(paramData);
                        baseUrl += paramData.length ? '?' + paramData : '';

                        form.action = baseUrl;
                        form.method = 'POST';
                        document.body.appendChild(form);
                        form.submit();
                    } else {
                        if (paramValue == defaultValue) {
                            delete paramData[paramName];
                        }
                        paramData = $.param(paramData);
                        location.href = baseUrl + (paramData.length ? '?' + paramData : '');
                    }
                }
            },

            makeAjaxCall: function (baseUrl, paramData) {
                var self = this;
                $.ajax({
                    url: baseUrl,
                    data: (paramData && paramData.length > 0 ? paramData + '&ajax=1' : 'ajax=1'),
                    type: 'get',
                    dataType: 'json',
                    cache: true,
                    showLoader: true,
                    timeout: 10000
                }).done(function (response) {
                    if (response.success) {
                        self.updateUrl(baseUrl, paramData);
                        self.updateContent(response.html);
                        self.setMessage({
                            type: 'success',
                            text: 'O conteúdo foi atualizado'
                        });

                    } else {
                        var msg = response.error_message;
                        if (msg) {
                            self.setMessage({
                                type: 'error',
                                text: msg
                            });
                        }
                    }
                }).fail(function (error) {
                    self.setMessage({
                        type: 'error',
                        text: 'Houve um errado inesperado. Por favor tente novamente.'
                    });

                });
            },
            setMessage: function (obj) {
                var messages = ko.observableArray([obj]);
                messageComponent().messages({
                    messages: messages
                });
            }
        });

        return $.mage.productListToolbarForm;
    });
