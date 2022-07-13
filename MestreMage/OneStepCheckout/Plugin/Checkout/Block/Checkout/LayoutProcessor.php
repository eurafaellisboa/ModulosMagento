<?php

namespace MestreMage\OneStepCheckout\Plugin\Checkout\Block\Checkout;

class LayoutProcessor
{

	public function afterProcess($subject, $jsLayout)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        if ($objectManager->get('MestreMage\OneStepCheckout\Helper\Config')->isEnabledOneStep()) {
            if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children'])) {
                if(isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['billing-address-form']))
                {
                    $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['billing-address-form']['component'] = 'MestreMage_OneStepCheckout/js/view/billing-address';
                }
//                foreach($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'] as $key => $value){
//                    if(isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'][$key]['component']) && $value['component'] == 'Magento_Checkout/js/view/billing-address')
//                    {
//                        $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'][$key]['component'] = 'MestreMage_OneStepCheckout/js/view/billing-address';
//                    }
//                }
                //print_r($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']);die;
            }
        }
        return $jsLayout;
    }
}