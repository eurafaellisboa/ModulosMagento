<?php
namespace MestreMage\CpfCnpj\Model\Checkout;

class LayoutProcessorPlugin
{
    /**
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */

    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array  $jsLayout
    )
    {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $scopeConfig = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        if($scopeConfig->getValue('mmcpfcnpj/geral/ativarmodulo', $storeScope)):

    if($scopeConfig->getValue('payment/yapay_credit_card/active', $storeScope)){
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['cnpj_customer']['sortOrder'] = 51;
    }else{
       // tipo pessoa
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['tipo_pessoa'] = [
            'component' => 'Magento_Ui/js/form/element/select',
            'config' => [
                'customScope' => 'shippingAddress',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/select',
                'id' => 'tipo-pessoa',
            ],
            'dataScope' => 'shippingAddress.drop_down',
            'label' => 'Tipo pessoa',
            'provider' => 'checkoutProvider',
            'visible' => true,
            'validation' => [],
            'sortOrder' => 50,
            'id' => 'tipo-pessoa',
            'options' => [
                [
                    'value' => '1',
                    'label' => 'Pessoa Física',
                ],
                [
                    'value' => '2',
                    'label' => 'Pessoa Jurídica',
                ]
            ]
        ];
    }


        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['vat_id'] = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'label' => __('CPF'),
            'config' => [
                'customScope' => 'shippingAddress',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/input',
            ],
            'provider' => 'checkoutProvider',
            'dataScope' => 'shippingAddress.vat_id',
            'validation' => ['required-entry' => true],
            'sortOrder' => 51,
        ];

            if($scopeConfig->getValue("mmcpfcnpj/loja/razao_social", $storeScope)):
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['razao_social'] = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'label' => __('Razão Social'),
            'config' => [
                'customScope' => 'shippingAddress',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/input',
            ],
            'provider' => 'checkoutProvider',
            'dataScope' => 'shippingAddress.razao_social',
            'sortOrder' => 53,
        ];
                endif;

            if($scopeConfig->getValue("mmcpfcnpj/loja/inscricao_estadual", $storeScope)):
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['inscricao_estadual'] = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'label' => __('Inscrição Estadual'),
            'config' => [
                'customScope' => 'shippingAddress',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/input',
            ],
            'provider' => 'checkoutProvider',
            'dataScope' => 'shippingAddress.inscricao_estadual',
            'sortOrder' => 53,
        ];

        endif;
/// tipo pessoa


        endif;
        return $jsLayout;
    }


}