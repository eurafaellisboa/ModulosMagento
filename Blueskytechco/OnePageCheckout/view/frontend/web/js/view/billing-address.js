define([
    'ko',
    'jquery',
    'underscore',
    'Magento_Ui/js/form/form',
    'Magento_Customer/js/model/customer',
    'Magento_Customer/js/model/address-list',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/create-billing-address',
    'Magento_Checkout/js/action/select-billing-address',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/model/checkout-data-resolver',
    'Magento_Customer/js/customer-data',
    'Magento_Checkout/js/action/set-billing-address',
    'Magento_Ui/js/model/messageList',
    'mage/translate',
    'uiRegistry'
], function (
    ko,
    $,
    _,
    Component,
    customer,
    addressList,
    quote,
    createBillingAddress,
    selectBillingAddress,
    checkoutData,
    checkoutDataResolver,
    customerData,
    setBillingAddressAction,
    globalMessageList,
    $t,
    registry
) {
    'use strict';

    var lastSelectedBillingAddress = null,
        newAddressOption = {
            /**
             * Get new address label
             * @returns {String}
             */
            getAddressInline: function () {
                return $t('New Address');
            },
            customerAddressId: null
        },
        countryData = customerData.get('directory-data'),
        addressOptions = addressList().filter(function (address) {
            return address.getType() == 'customer-address'; //eslint-disable-line eqeqeq
        });

    addressOptions.push(newAddressOption);

    return Component.extend({
        defaults: {
            template: 'Blueskytechco_OnePageCheckout/billing-address'
        },

        currentBillingAddress: quote.billingAddress,
        addressOptions: addressOptions,
        customerHasAddresses: addressOptions.length > 1,

        /**
         * Init component
         */
        initialize: function () {
            var self = this;
            this._super();
            quote.paymentMethod.subscribe(function () {
                if (self.isAddressSameAsShipping()) {
                    selectBillingAddress(quote.shippingAddress());
                }
                checkoutDataResolver.resolveBillingAddress();
            }, this);
        },

        /**
         * @return {exports.initObservable}
         */
        initObservable: function () {
            this._super()
                .observe({
                    selectedAddress: null,
                    isAddressDetailsVisible: quote.billingAddress() != null,
                    isAddressFormVisible: !customer.isLoggedIn() || addressOptions.length === 1,
                    isAddressSameAsShipping: false,
                    saveInAddressBook: 1
                });

            quote.billingAddress.subscribe(function (newAddress) {
                if (quote.isVirtual() || !quote.shippingAddress()) {
                    this.isAddressSameAsShipping(false);
                } else {
                    this.isAddressSameAsShipping(
                        newAddress != null &&
                        newAddress.getCacheKey() == quote.shippingAddress().getCacheKey() //eslint-disable-line eqeqeq
                    );
                }

                if (newAddress != null && newAddress.saveInAddressBook !== undefined) {
                    this.saveInAddressBook(newAddress.saveInAddressBook);
                } else {
                    this.saveInAddressBook(1);
                }
                this.isAddressDetailsVisible(true);
            }, this);

            return this;
        },

        canUseShippingAddress: ko.computed(function () {
            return !quote.isVirtual() && quote.shippingAddress() && quote.shippingAddress().canUseForBilling();
        }),

        /**
         * @param {Object} address
         * @return {*}
         */
        addressOptionsText: function (address) {
            return address.getAddressInline();
        },

        /**
         * @return {Boolean}
         */
        useShippingAddress: function () {
            if (this.isAddressSameAsShipping()) {
                selectBillingAddress(quote.shippingAddress());

                this.updateAddresses();
                this.isAddressDetailsVisible(true);
            } else {
                lastSelectedBillingAddress = quote.billingAddress();
                quote.billingAddress(null);
                this.isAddressDetailsVisible(false);
            }
            checkoutData.setSelectedBillingAddress(null);

            return true;
        },

        /**
         * Update address action
         */
        updateAddress: function () {
            var addressData, newBillingAddress, update;

            if (this.selectedAddress() && this.selectedAddress() != newAddressOption) { //eslint-disable-line eqeqeq
                selectBillingAddress(this.selectedAddress());
                checkoutData.setSelectedBillingAddress(this.selectedAddress().getKey());
                update = true;
            } else {
                this.source.set('params.invalid', false);
                this.source.trigger(this.dataScopePrefix + '.data.validate');

                if (this.source.get(this.dataScopePrefix + '.custom_attributes')) {
                    this.source.trigger(this.dataScopePrefix + '.custom_attributes.data.validate');
                }

                if (!this.source.get('params.invalid')) {
                    addressData = this.source.get(this.dataScopePrefix);

                    if (customer.isLoggedIn() && !this.customerHasAddresses) { //eslint-disable-line max-depth
                        this.saveInAddressBook(1);
                    }
                    addressData['save_in_address_book'] = this.saveInAddressBook() ? 1 : 0;
                    newBillingAddress = createBillingAddress(addressData);

                    // New address must be selected as a billing address
                    selectBillingAddress(newBillingAddress);
                    checkoutData.setSelectedBillingAddress(newBillingAddress.getKey());
                    checkoutData.setNewCustomerBillingAddress(addressData);
                    update = true;
                }
            }
            if (!_.isUndefined(update)) {
                this.updateAddresses();
            }
        },

        /**
         * Edit address action
         */
        editAddress: function () {
            lastSelectedBillingAddress = quote.billingAddress();
            quote.billingAddress(null);
            this.isAddressDetailsVisible(false);
        },

        /**
         * Cancel address edit action
         */
        cancelAddressEdit: function () {
            this.restoreBillingAddress();

            if (quote.billingAddress()) {
                // restore 'Same As Shipping' checkbox state
                this.isAddressSameAsShipping(
                    quote.billingAddress() != null &&
                    quote.billingAddress().getCacheKey() == quote.shippingAddress().getCacheKey() && //eslint-disable-line
                    !quote.isVirtual()
                );
                this.isAddressDetailsVisible(true);
            }
        },

        /**
         * Restore billing address
         */
        restoreBillingAddress: function () {
            if (lastSelectedBillingAddress != null) {
                selectBillingAddress(lastSelectedBillingAddress);
            }
        },

        /**
         * @param {Object} address
         */
        onAddressChange: function (address) {
            if (address == newAddressOption) {
                this.autoFillAddress('co-billing-form');
            }
            this.isAddressFormVisible(address == newAddressOption); //eslint-disable-line eqeqeq
        },

        /**
         * @param {Number} countryId
         * @return {*}
         */
        getCountryName: function (countryId) {
            return countryData()[countryId] != undefined ? countryData()[countryId].name : ''; //eslint-disable-line
        },

        /**
         * Trigger action to update shipping and billing addresses
         */
        updateAddresses: function () {
            if (window.checkoutConfig.reloadOnBillingAddress ||
                !window.checkoutConfig.displayBillingOnPaymentMethod
            ) {
                setBillingAddressAction(globalMessageList);
            }
        },

        /**
         * Get code
         * @param {Object} parent
         * @returns {String}
         */
        getCode: function (parent) {
            return _.isFunction(parent.getCode) ? parent.getCode() : 'shared';
        },

        /**
         * Auto Fill Address
         * @param element
         */
        autoFillAddress: function (element) {
            var self = this;
            if (typeof(element)  === "object") {
                var formId = element.id;
            } else if (typeof(element)  === "string") {
                var formId = element.replace("#", "");
            }
            if (addressList().length === 0 || typeof(element)  === "string") {
                setTimeout(function(){
                    var streetId = $('#' + formId + ' [name="street[0]"]').id;
                    var street = $('#' + formId + ' [name="street[0]"]').val();
                    if (street == '') {
                        self.fillCountry(formId);
                    }
                }, 1000);
            }

        },

        /**
         * Fill Address
         * @param address
         * @param id
         */
        fillInAddress: function (address, id) {
            var componentFields = [
                'country_id',
                'postcode',
                'region_id',
                'region',
                'city',
                'street'
            ];
            var country = false,
                countryList = window.checkoutConfig.OnePageCheckout.googleApiListCountries,
                useRegionId = false,
                countryElement = false,
                regionIdElement = false,
                component = 'checkout.steps.billing-step.payment.payments-list.billing-address-form-shared.form-fields';
            registry.get(component, function (formComponent) {
                $.each(componentFields, function (index, field) {
                    var element = formComponent.getChild(field);
                    if (field === 'region') {
                        element = formComponent.getChild('region_id_input');
                    }

                    if (field == 'country_id' && field in address) {
                        $('#' + element.uid).find('option').each(function () {
                            if ($(this).attr('value') == address[field]) {
                                var currentCountry = element.value();
                                element.value(address[field]);
                                country = address[field];
                                countryElement = element;
                                if (($.inArray(currentCountry, countryList) === -1 && $.inArray(address[field], countryList) !== -1) ||
                                    ($.inArray(currentCountry, countryList) !== -1 && $.inArray(address[field], countryList) !== -1 && currentCountry != address[field])
                                ) {
                                    element.trigger('change');
                                }
                                return false;
                            }
                        });
                    }
                    if (field == 'region_id' && field in address && country != false && $.inArray(country, countryList) !== -1) {
                        $('#' + element.uid).find('option').each(function () {
                            if ($(this).attr('data-title') == address[field]) {
                                element.value($(this).attr('value'));
                                regionIdElement = element;
                                return false;
                            }
                        });
                        useRegionId = true;
                    }
                    if (field == 'region' && country != false && useRegionId == false) {
                        if ('region_id' in address) {
                            element.value(address['region_id']);
                        } else {
                            element.value('');
                        }
                    }
                    if (field == 'street' && field in address) {
                        element = formComponent.getChild(field).getChild(0);
                        element.value(address[field]);
                    }
                    if ((field == 'postcode' || field == 'city')) {
                        if (field in address) {
                            element.value(address[field]);
                        } else {
                            element.value('');
                        }
                    }
                });
            });

            if (country != '' && component == shipping) {
                if (useRegionId == true && regionIdElement != false) {
                    regionIdElement.trigger('change');
                } else {
                    if (countryElement != false) {
                        countryElement.trigger('change');
                    }
                }
            }
        },

        /**
         * Fill Country
         * @param id
         * @param countryCode
         */
        fillCountry: function (formId) {
            var countryCode = window.checkoutConfig.OnePageCheckout.googleApiCustomerCountry,
                countrySelector = $('#' + formId + ' [name="country_id"]'),
                currentCountry = countrySelector.val();
            if (currentCountry == countryCode) {
                return;
            }
            countrySelector.find('option').each(function () {
                if ($(this).attr('value') == countryCode) {
                    countrySelector.val(countryCode);
                    countrySelector.trigger('change');
                    return false;
                }
            });
        },

        /**
         * Get customer attribute label
         *
         * @param {*} attribute
         * @returns {*}
         */
        getCustomAttributeLabel: function (attribute) {
            var resultAttribute;

            if (typeof attribute === 'string') {
                return attribute;
            }

            if (attribute.label) {
                return attribute.label;
            }

            resultAttribute = _.findWhere(this.source.get('customAttributes')[attribute['attribute_code']], {
                value: attribute.value
            });

            return resultAttribute && resultAttribute.label || attribute.value;
        }
    });
});
