define([
    'jquery',
    'jquery-ui-modules/widget',
    'Lof_ShippingCalculator/js/view/shipping-rates',
    'mage/template',
    'Magento_Catalog/js/price-utils',
    'Magento_Ui/js/modal/alert',
    'mage/translate',
    'Magento_Checkout/js/model/url-builder',
    'mage/url',
    'Magento_Customer/js/model/customer',
    'mage/storage',
    'underscore',
    'validation'
    ], function(
        $, 
        jqueyUi, 
        ShippingRateRender,
        mageTemplate, 
        utils, 
        alert, 
        $t,
        urlBuilder,
        mageurl,
        customer,
        storage
        ) {
        var deferred = $.Deferred();
        
        $.widget('lof.shippingEstimization', {
            options: {
                productFormId: '#product_addtocart_form',
                countryId: '#country',
                updateElements: '[data-update]',
                //priceTemplate: '<span class="price"><%- data.formatted %></span>',
                usePriceInclucdingTax: false,
                emptyMsg: 'There are no shipping methods available for this region.',
                autoCalculate: false,
                enableCheapest: false,
                storeCode: 'default'
            },
            _create: function() {
               this._assignVariables();
               if (this.$productForm.length) {
                    this._prepareHtml();
                    this._attacheEvents();
               }
            },
            _assignVariables: function() {
                var self = this, conf = this.options;
                this.$form = self.element.find('form').first();
                this.$countryBox = this.element.find(conf.countryId);
                this.$countryBox.attr('data-update', 'country_id');
                var fieldName = this.$countryBox.attr('name');
                this.$countryBox.attr('data-name', fieldName);
                this.$countryBox.removeAttr('name');
                this.$currentAddress = self.element.find('[data-role=current-address]').first();
                this.addressTemplate = mageTemplate(conf.addressFormat);
                
                this.$productForm = $(conf.productFormId);
                this.$loader = this.element.find('[data-role=loader]').hide();
                this.$result = this.element.find('[data-role=result]');
                this.$rsContainer = this.element.find('[data-role=rs-container]');
                if (this.$productForm.length) {
                    this.$qty = $('[name=qty]', this.$productForm);
                    if (this.$qty.length) {
                        this.$qty.attr('data-update', 'qty');
                        this.$qty.attr('data-name', 'qty');
                    }
                    this.productId = this.$productForm.find('[name=product]').first().val();
                }
            },
            _prepareHtml: function() {
                this.element.find('.field.country,.field.region,.field.zip').removeClass('required');
                this._updateAddressLabel();
            },
            _attacheEvents: function() {
                var self = this, conf = this.options;
                if (conf.autoCalculate) {
                    self.element.find('[data-update]').on('change', this._updateShippingCost.bind(this));
                    self.$productForm.on('change', 'input,select', this._updateShippingCost.bind(this));
                    self.$productForm.on('change', '.swatch-option', this._updateShippingCost.bind(this));
                    this._updateShippingCost();
                }
                this.element.find('input[type=text]').on('keypress', function(e) {
                    if (e.keyCode == 13) {
                        self._updateShippingCost();
                        return false;
                    }
                    return true;
                });
                self.element.on('change', 'select,input', function() {
                    self._updateAddressLabel();
                });
                self.element.find('[data-role=submit]').click(this._updateShippingCost.bind(this));
                self.element.find('[data-role=content-toggle]').click(function() {
                    self.element.find('[data-role=block-content]').slideToggle(300);
                    self.element.toggleClass('opened');
                });
                
            },
            _updateAddressLabel: function() {
                var self = this, conf = this.options;
                var country = self.$countryBox.val().toLowerCase();
                var data = {};
                if (country) {
                    if (conf.displayFlag) {
                        self.element.find('img[data-role=country-flag]').attr('src', conf.flagUrl.replace('{{code}}', country));
                    }
                }
                if (conf.addressFormat) {
                    var pattern = new RegExp(/<%- (.*?) %>/g);
                    var hasEmpty = false;
                    if (pattern.test(conf.addressFormat)) {
                        $.each(conf.addressFormat.match(pattern), function(ii, m) {
                            var name = m.replace('<%-','').replace('%>','').trim();
                            if (typeof data[name] == 'undefined') {
                                var $input = self.element.find('[data-name="' + name + '"]');
                                if ($input.length) {
                                    var value = $input.val();
                                    if (value) {
                                        if ($input.get(0).tagName.toLowerCase() == 'select') {
                                            value = $input.find('option[value="' + value + '"]').text();
                                        }
                                        data[name] = value;
                                    } else {
                                        hasEmpty = true;
                                    }
                                }
                            }
                        });
                    }
                    if (!hasEmpty) {
                        if (self.element.find('[data-name=region]').css('display') == 'none') {
                            var regionName = self.element.find('[data-name=region_id]').val();
                            regionName = regionName ? self.element.find('[data-name=region_id] option[value="' + regionName + '"]').text() : '';
                            data.region_name = regionName;
                        } else {
                            data.region_name = self.element.find('[data-name=region]').val();
                        }
                        if ((data.region_name) && (conf.addressFormat.search('region_name') > -1)) {
                            self.$currentAddress.html(self.addressTemplate(data));
                        } else {
                            self.$currentAddress.html('');
                        }
                    } else {
                        self.$currentAddress.html('');
                    }
                }
            },
            _getFormArray: function($form) {
                var fields = $form.serializeArray();
                var rs = {};
                var super_attributes = [];
                $.each(fields, function(i, field) {
                    if (field.name.indexOf("super_attribute") !== -1) {
                        var new_name = field.name.replace("super_attribute","").replace("[","").replace("]","");
                        new_name = String(new_name).trim();

                        super_attributes.push(new_name+":"+field.value)
                    } else {
                        rs[field.name] = field.value;
                    }
                });
                rs["super_attribute"] = super_attributes.join(",");
                return rs;
            },
            _updateShippingCost: function() {
                var self = this, conf = this.options;
                var data = {};
                if (!self.$productForm.valid()) {
                    return false;
                }
                
                data.product_id =  this.productId;
                self.element.find('[data-update]').each(function() {
                    var $input = $(this);
                    var name = $input.attr('data-name');
                    if (name) {
                        data[name] = $input.val();
                    }
                });
                
                self.$productForm.find('[data-update]').each(function() {
                    var $input = $(this);
                    var name = $input.attr('name');
                    if (name) {
                        data[name] = $input.val();
                    } else {
    
                    }
                });
                var postData = this._getFormArray(this.$productForm);
                postData.shipping_data = data;
                if (data.country_id) {
                    if (typeof(postData.item) !== "undefined") {
                        delete postData.item;
                    }
                    if (typeof(postData.form_key) !== "undefined") {
                        delete postData.form_key;
                    }
                    if (typeof(window.checkoutConfig) == 'undefined' || typeof(window.checkoutConfig.storeCode) == 'undefined') {
                        window.checkoutConfig = {"storeCode": conf.storeCode }
                    }
                    var serviceUrl = urlBuilder.createUrl('/lofShippingCalculator/getRates', {});
                    var url = mageurl.build(serviceUrl, {});
                    
                    self.$loader.show();
                    var correctPostData = {
                        "product": postData.product,
                        "qty": postData.qty,
                        "related_product": postData.related_product,
                        "selected_configurable_option": postData.selected_configurable_option,
                        "super_attribute": postData.super_attribute,
                        "shipping_data": postData.shipping_data
                    }
                    storage.post(
                        url, 
                        JSON.stringify({"request": correctPostData }), 
                        false
                    ).done(function (response) {
                        self.$loader.hide();
                        if (typeof response != 'object') {
                            return false;
                        }
                        if (response.length) {
                            self.$result.html(ShippingRateRender({
                                carriers: response,
                                enableCheapest: conf.enableCheapest,
                                priceFormat: conf.priceFormat,
                                usePriceInclucdingTax: conf.usePriceInclucdingTax
                            }));
                            self.$rsContainer.fadeIn(300);
                        } else {
                            self.$result.html('<div class="empty-msg">' + conf.emptyMsg + '</div>');
                        }
                        deferred.resolve();
                    }).fail(function (response) {
                        self.$loader.hide();
                        deferred.reject();
                    });
                }
            }
        });
        return $.lof.shippingEstimization;
    });