<?php
/**

 *
 * @category Nuzz
 * @package  Nuzz/CheckoutDob
 * @author   Rafael Lisboa <rafael@nuzz.com.br>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     https://nuzz.com.br
 */

namespace Nuzz\CheckoutDob\Block;

class LayoutProcessor implements \Magento\Checkout\Block\Checkout\LayoutProcessorInterface
{
    /**
     * Add custom field to checkout layout
     * @param array $jsLayout
     * @return array
     */
    public function process($jsLayout)
    {
        $attributeCode = 'dob';
        $fieldConfiguration = [
            'component' => 'Magento_Ui/js/form/element/date',
            'config' => [
                'customScope' => 'shippingAddress.extension_attributes',
                'customEntry' => null,
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/date',
                //'tooltip' => [
                    //'description' => 'Here you can leave delivery notes',
                //],
            ],
            'dataScope' => 'shippingAddress.extension_attributes' . '.' . $attributeCode,
            'label' => 'DOB',
            'provider' => 'checkoutProvider',
            'sortOrder' => 1000,
            'validation' => [
                'required-entry' => true
            ],
            'options' => [],
            'filterBy' => null,
            'customEntry' => null,
            'visible' => true,
            'value' => ''
        ];

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
        ['children']['shippingAddress']['children']['shipping-address-fieldset']
        ['children'][$attributeCode] = $fieldConfiguration;
        return $jsLayout;
    }
}
