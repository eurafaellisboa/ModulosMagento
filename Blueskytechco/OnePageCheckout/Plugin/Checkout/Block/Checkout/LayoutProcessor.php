<?php
namespace Blueskytechco\OnePageCheckout\Plugin\Checkout\Block\Checkout;
use Magento\Customer\Model\AttributeMetadataDataProvider;
use Amazon\Core\Helper\Data;
use Magento\Checkout\Block\Checkout\AttributeMerger;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Attribute;
use Magento\Customer\Model\Options;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\Form\AttributeMapper;
/**
 * Class LayoutProcessor
 * @package Blueskytechco\OnePageCheckout\Plugin\Checkout\Block\Checkout
 */
class LayoutProcessor
{
    /**
     * One step checkout helper
     *
     * @var Config
     */
    protected $configHelper;
    /**
     * LayoutProcessor constructor.
     * @param Config $configHelper
     */

    /**
     * @var AttributeMetadataDataProvider
     */
    private $attributeMetadataDataProvider;

    /**
     * @var AttributeMapper
     */
    protected $attributeMapper;

    /**
     * @var AttributeMerger
     */
    protected $merger;

    /**
     * @var Options
     */
    private $options;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    public function __construct(
        \Blueskytechco\OnePageCheckout\Helper\Address $configHelper,
        CheckoutSession $checkoutSession,
        AttributeMetadataDataProvider $attributeMetadataDataProvider,
        AttributeMapper $attributeMapper,
        AttributeMerger $merger
    ) {
        $this->configHelper = $configHelper;
        $this->checkoutSession               = $checkoutSession;
        $this->attributeMetadataDataProvider = $attributeMetadataDataProvider;
        $this->attributeMapper               = $attributeMapper;
        $this->merger                        = $merger;
    }

    /**
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array $jsLayout
    ) {
        if (!$this->configHelper->getModuleStatus()) {
            return $jsLayout;
        }
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
            ['children']['payment']['children']['afterMethods']['children']['billing-address-form'])) {
            $component = $jsLayout['components']['checkout']['children']['steps']['children']
            ['billing-step']['children']['payment']['children']['afterMethods']['children']
            ['billing-address-form'];
            unset(
                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
                ['children']['payment']['children']['afterMethods']['children']['billing-address-form']
            );
            $component['component'] = 'Blueskytechco_OnePageCheckout/js/view/billing-address';
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
            ['children']['payment']['children']['payments-list']['children']
            ['billing-address-form-shared'] = $component;
        }
        if($orderCommentTitle = $this->configHelper->getGeneral('title_box_order_comment'))
        {
            $jsLayout['components']['checkout']['children']['sidebar']['children']
            ['rokanthemes_opc_order_comment']['label'] = $orderCommentTitle;
        }
        if (!$this->configHelper->isDisplayField('show_order_comment')) {
            unset(
                $jsLayout['components']['checkout']['children']['sidebar']['children']
                ['rokanthemes_opc_order_comment']
            );
        }

        $jsLayout = $this->sortAddressComponent($jsLayout);
        $jsLayout = $this->newsletter($jsLayout);
        $jsLayout = $this->_prepareShippingDelivery($jsLayout);

        if (!$this->configHelper->isDisplayField('show_gift_message') ||
            !$this->configHelper->isMessagesAllowed()) {
            unset(
                $jsLayout['components']['checkout']['children']['sidebar']['children']
                ['giftmessage']
            );
        }

        $jsLayout = $this->discountCode($jsLayout);

        $jsLayout = $this->removeComponent($jsLayout);
        return $jsLayout;
    }
    protected function _prepareShippingDelivery($jsLayout)
    {
        if($boxTitle = $this->configHelper->getGeneral('title_box_shipping_delivery_date'))
        {
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']
            ['before-shipping-method-form']['children']
            ['rokanthemes_opc_shipping_delivery_date']['label'] = $boxTitle;
        }
        if($boxTitle = $this->configHelper->getGeneral('title_box_shipping_delivery_comment'))
        {
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']
            ['before-shipping-method-form']['children']
            ['rokanthemes_opc_shipping_delivery_comment']['label'] = $boxTitle;
        }
        if (!$this->configHelper->isDisplayField('show_shipping_delivery_date')) {
            unset(
                $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                ['shippingAddress']['children']
                ['before-shipping-method-form']['children']
                ['rokanthemes_opc_shipping_delivery_date']
            );
        }
        if (!$this->configHelper->isDisplayField('show_shipping_delivery_comment')) {
            unset(
                $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                ['shippingAddress']['children']
                ['before-shipping-method-form']['children']
                ['rokanthemes_opc_shipping_delivery_comment']
            );
        }
        return $jsLayout;
    }
    /**
     * @return Options
     */
    private function getOptions()
    {
        if (!is_object($this->options)) {
            $this->options = ObjectManager::getInstance()->get(Options::class);
        }

        return $this->options;
    }
    protected function sortAddressComponent($jsLayout)
    {
        $steps = &$jsLayout['components']['checkout']['children']['steps']['children'];

        $shippingStep = &$steps['shipping-step']['children'];
        /** Shipping address fields */
        if (isset($shippingStep['shippingAddress']['children']['shipping-address-fieldset']['children'])) {
            $shipping = &$shippingStep['shippingAddress']['children'];

            $shipping['shipping-address-fieldset']['children'] = $this->getAddressFieldset(
                $shipping['shipping-address-fieldset']['children'],
                'shippingAddress'
            );
            if (isset($shipping['shipping-address-fieldset']['children']['taxvat'])) {
                $shipping['shipping-address-fieldset']['children']['taxvat']['dataScope'] = 'shippingAddress.vat_id';
            }
            if ($this->configHelper->isEnableAmazonPay()) {
                $shippingConfig = &$shippingStep['shippingAddress'];

                $shippingConfig['component']                               = 'Blueskytechco_OnePageCheckout/js/view/shipping';
                $shippingConfig['children']['customer-email']['component'] = 'Blueskytechco_OnePageCheckout/js/view/form/element/email';
            }

        }

        /** Billing address fields */
        if (isset($shippingStep['billingAddress']['children']['billing-address-fieldset']['children'])) {
            $billing = &$shippingStep['billingAddress']['children'];

            $billing['billing-address-fieldset']['children'] = $this->getAddressFieldset(
                $billing['billing-address-fieldset']['children'],
                'billingAddress'
            );

            /** Fix the issue of the unsaved vat_id field */
            if (isset($billing['billing-address-fieldset']['children']['taxvat'])) {
                $billing['billing-address-fieldset']['children']['taxvat']['dataScope'] = 'billingAddress.vat_id';
            }
            /** Remove billing customer email if quote is not virtual */
            if (!$this->checkoutSession->getQuote()->isVirtual()) {
                unset($billing['customer-email']);
            }
        }
        $billingStep = &$steps['billing-step']['children'];
        if(isset($billingStep['payment']['children']['payments-list']['children']['billing-address-form-shared']['children']['form-fields']['children']))
        {
            $billingStep['payment']['children']['payments-list']['children']['billing-address-form-shared']['children']['form-fields']['children'] = $this->sortFieldAddress(
                $billingStep['payment']['children']['payments-list']['children']['billing-address-form-shared']['children']['form-fields']['children']
            );
        }
        /** Remove billing address in payment method content */
        /** @var array $fields */
        $fields = &$billingStep['payment']['children']['payments-list']['children'];
        foreach ($fields as $code => $field) {
            if (array_key_exists('component', $field) &&
                $field['component'] === 'Magento_Checkout/js/view/billing-address') {
                unset($fields[$code]);
            }
        }
        return $jsLayout;
    }
    protected function sortFieldAddress($fields)
    {
        $fieldPosition = $this->configHelper->getAddressFieldPosition();
        foreach ($fields as $key => $value)
        {
            if(isset($fieldPosition[$key]['sortOrder']))
            {
                $fields[$key]['sortOrder'] = $fieldPosition[$key]['sortOrder'];
            }
            if(isset($fieldPosition[$key]['required']))
            {
                $fields[$key]['validation']['required-entry'] = $fieldPosition[$key]['required'];
            }
        }
        return $fields;
    }
    /**
     * Get address fieldset for shipping/billing address
     *
     * @param $fields
     * @param $type
     *
     * @return array
     * @throws LocalizedException
     */
    public function getAddressFieldset($fields, $type)
    {
        $elements = $this->getAddressAttributes($fields);

        $systemAttribute = $elements['default'];
        if (count($systemAttribute)) {
            $attributesToConvert = [
                'prefix' => [$this->getOptions(), 'getNamePrefixOptions'],
                'suffix' => [$this->getOptions(), 'getNameSuffixOptions'],
            ];
            $systemAttribute     = $this->convertElementsToSelect($systemAttribute, $attributesToConvert);
            $fields              = $this->merger->merge(
                $systemAttribute,
                'checkoutProvider',
                $type,
                $fields
            );
        }

        $customAttribute = $elements['custom'];
        if (count($customAttribute)) {
            $fields = $this->merger->merge(
                $customAttribute,
                'checkoutProvider',
                $type . '.custom_attributes',
                $fields
            );
        }

        $fieldPosition = $this->configHelper->getAddressFieldPosition();

        $opcField        = [];
        $allFieldSection = $this->configHelper->getSortedField(false);
        foreach ($allFieldSection as $allField) {
            /** @var Attribute $field */
            foreach ($allField as $field) {
                $opcField[] = $field->getAttributeCode();
            }
        }

        $this->addCustomerAttribute($fields, $type);
        $this->addAddressOption($fields, $fieldPosition, $opcField);

        /**
         * Compatible Amazon Pay
         */
        if ($this->configHelper->isEnableAmazonPay()) {
            /** @var Data $amazonHelper */
            $amazonHelper = $this->configHelper->getObject(Data::class);
            if ($amazonHelper->isPwaEnabled()) {
                $fields['inline-form-manipulator'] = [
                    'component' => 'Blueskytechco_OnePageCheckout/js/view/amazon'
                ];
            }
        }

        return $fields;
    }

    /**
     * @param array $fields
     * @param array $fieldPosition
     * @param array $opcField
     *
     * @return $this
     */
    private function addAddressOption(&$fields, $fieldPosition, $opcField = [])
    {
        foreach ($fields as $code => &$field) {
            if (empty($fieldPosition[$code])) {
                if ($code === 'country_id') {
                    $field['config']['additionalClasses'] = 'mp-hidden';
                    continue;
                }
                continue;
            }

            $fieldConfig = $fieldPosition[$code];

            if (in_array($code, $opcField, true)) {
                $field['sortOrder'] = $fieldConfig['sortOrder'];
            }

            $classes = $field['config']['additionalClasses'] ?? '';
            $classes .= ' col-mp mp-' . $fieldConfig['colspan'];
            if ($fieldConfig['isNewRow']) {
                $classes .= ' mp-clear';
            }
            if (isset($fieldConfig['required'])) {
                if ($fieldConfig['required']) {
                    $classes .= ' required';

                    $field['validation']['required-entry'] = true;
                } else {
                    $classes .= ' not-required';

                    $validation = &$field['validation'];
                    if (isset($validation['required-entry'])) {
                        unset($validation['required-entry']);
                    }
                    if (isset($validation['min_text_length'])) {
                        unset($validation['min_text_length']);
                    }
                }
            }

            $field['config']['additionalClasses'] = $classes;
        }
        unset($field);
        return $this;
    }

    /**
     * Add customer attribute like gender, dob, taxvat
     *
     * @param $fields
     * @param $type
     *
     * @return $this
     * @throws LocalizedException
     */
    private function addCustomerAttribute(&$fields, $type)
    {
        $attributes      = $this->attributeMetadataDataProvider->loadAttributesCollection(
            'customer',
            'customer_account_create'
        );
        $addressElements = [];
        foreach ($attributes as $attribute) {
            if ($this->configHelper->isCustomerAttributeVisible($attribute)) {
                $addressElements[$attribute->getAttributeCode()] = $this->attributeMapper->map($attribute);
            }
        }

        if (count($addressElements)) {
            $fields = $this->merger->merge(
                $addressElements,
                'checkoutProvider',
                $type . '.custom_attributes',
                $fields
            );
        }

        foreach ($fields as $code => &$field) {
            if (isset($field['label'])) {
                $field['label'] = __($field['label']);
            }
        }

        return $this;
    }
    /**
     * Convert elements(like prefix and suffix) from inputs to selects when necessary
     *
     * @param array $elements address attributes
     * @param array $attributesToConvert fields and their callbacks
     *
     * @return array
     */
    private function convertElementsToSelect($elements, $attributesToConvert)
    {
        $codes = array_keys($attributesToConvert);
        foreach (array_keys($elements) as $code) {
            if (!in_array($code, $codes, true)) {
                continue;
            }
            $options = call_user_func($attributesToConvert[$code]);
            if (!is_array($options)) {
                continue;
            }
            $elements[$code]['dataType']    = 'select';
            $elements[$code]['formElement'] = 'select';

            foreach ($options as $key => $value) {
                $elements[$code]['options'][] = [
                    'value' => $key,
                    'label' => $value,
                ];
            }
        }
        return $elements;
    }
    /**
     * @param array $fields
     *
     * @return array
     * @throws LocalizedException
     */
    private function getAddressAttributes($fields)
    {
        $elements  = [
            'custom'  => [],
            'default' => []
        ];
        $formCodes = ['onestepcheckout_index_index', 'customer_register_address'];
        foreach ($formCodes as $formCode) {
            $attributes = $this->attributeMetadataDataProvider->loadAttributesCollection(
                'customer_address',
                $formCode
            );

            /** @var Attribute $attribute */
            foreach ($attributes as $attribute) {
                $code = $attribute->getAttributeCode();

                if (isset($elements['custom'][$code]) || isset($elements['default'][$code])) {
                    continue;
                }

                $element = $this->attributeMapper->map($attribute);
                if (isset($element['label'])) {
                    $label            = $element['label'];
                    $element['label'] = __($label);
                }

                if ($attribute->getIsUserDefined()) {
                    if (!isset($fields[$code])) {
                        $elements['custom'][$code] = $element;
                    }
                } else {
                    $elements['default'][$code] = $element;
                }
            }
        }

        return $elements;
    }
    /**
     * @param $jsLayout
     * @return mixed
     */
    protected function removeComponent($jsLayout)
    {
        unset(
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
            ['children']['payment']['children']['afterMethods']['children']['discount']
        );

        unset(
            $jsLayout['components']['checkout']['children']['sidebar']['children']['shipping-information']
        );

        unset(
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
            ['children']['payment']['children']['payments-list']['children']['before-place-order']
            ['children']['agreements']
        );

        unset($jsLayout['components']['checkout']['children']['progressBar']);
        if(isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['customer-email']['children']['recaptcha'])){
            unset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['customer-email']['children']['recaptcha']);
        }
        if(isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['customer-email']['children']['recaptcha'])){
            unset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['customer-email']['children']['recaptcha']);
        }
        if(isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['beforeMethods']['children']['place-order-recaptcha-container'])){
            unset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['beforeMethods']['children']['place-order-recaptcha-container']);
        }
        if(isset($jsLayout['components']['checkout']['children']['authentication']['children']['recaptcha'])){
            unset($jsLayout['components']['checkout']['children']['authentication']['children']['recaptcha']);
        }
       
        return $jsLayout;
    }

    /**
     * @param $jsLayout
     * @return mixed
     */
    protected function newsletter($jsLayout)
    {
        $checked = false;
        if ($checked = $this->configHelper->getGeneral('default_newletter_checkbox')) {
            $jsLayout['components']['checkout']['children']['sidebar']['children']
            ['subscribe']['config']['checked'] = $checked;
        }
        if($newletterTitle = $this->configHelper->getGeneral('title_box_subscribe'))
        {
            $jsLayout['components']['checkout']['children']['sidebar']['children']
            ['subscribe']['config']['description'] = $newletterTitle;
        }
        if(!$this->configHelper->isDisplayField('show_subscribe_newsletter'))
        {
            unset($jsLayout['components']['checkout']['children']['sidebar']['children']
                ['subscribe']);
        }
        return $jsLayout;
    }

    /**
     * @param $jsLayout
     * @return mixed
     */
    protected function discountCode($jsLayout)
    {
        if ($this->configHelper->isDisplayField('show_discount_box')) {
            $jsLayout['components']['checkout']['children']['sidebar']['children']['discount'] =
                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
                ['children']['payment']['children']['afterMethods']['children']['discount'];

            $jsLayout['components']['checkout']['children']['sidebar']['children']['discount']
            ['displayArea'] = 'summary';
            $jsLayout['components']['checkout']['children']['sidebar']['children']['discount']
            ['template'] = 'Blueskytechco_OnePageCheckout/payment/discount';

            $jsLayout['components']['checkout']['children']['sidebar']['children']['discount']
            ['sortOrder'] = 240;
        }
        return $jsLayout;
    }
}
