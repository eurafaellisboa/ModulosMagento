<?php
namespace Blueskytechco\CustomCatalog\Plugin;

class CustomerData {

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Checkout\Helper\Data
     */
    protected $checkoutHelper;

    /**
     * @param \Magento\Checkout\Helper\Data $checkoutHelper
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Helper\Data $checkoutHelper
    ){
        $this->scopeConfig = $scopeConfig;
        $this->checkoutHelper = $checkoutHelper;
    }

    public function afterGetSectionData(\Magento\Checkout\CustomerData\Cart $subject, $result)
    {
        // Get the previous data
        $data = $result;

        // Append variable
        $active = $this->getConfigData('carriers/ups/active');
        $shipping_subtotal = $this->getConfigData('carriers/ups/free_shipping_subtotal');
        $html = '';
        $data['shipping_free'] = '';
        $data['shipping_free_canvas'] = false;
        if ($active) {
            $subtotal_amount = $data['subtotalAmount'];
            $summary_count = $data['summary_count'];
            if (!$shipping_subtotal) {
                $shipping_subtotal = 0;
            }
            $price = $this->checkoutHelper->formatPrice($shipping_subtotal);
            if ($summary_count > 0) {
                if ($shipping_subtotal > 0 && $shipping_subtotal > $subtotal_amount) {
                    $percent = ($subtotal_amount/$shipping_subtotal)*100;
                    $remaining_price = $shipping_subtotal - $subtotal_amount;
                    $remaining_price = $this->checkoutHelper->formatPrice($remaining_price);
                    $html .= '<div class="cart_thres_2">';
                        $html .= __('Almost there, add %1 more to get %2', $remaining_price, __('<span>FREE SHIPPING!</span>'));
                    $html .= '</div>';
                    $html .= '<div class="cart_bar_w">';
                        $html .= '<span style="width: '.$percent.'%;"></span>';
                    $html .= '</div>';
                } else {
                    $html .= '<div class="cart_thres_3">';
                        $html .= __("%1 You've got free shipping!", __('<span>Congratulations!</span>'));
                    $html .= '</div>';
                    $data['shipping_free_canvas'] = 1;
                }
            } else {
                $html .= '<div class="cart_thres_1">';
                    $html .= __('Free Shipping for all orders over %1', $price);
                $html .= '</div>';
            }
            $data['shipping_free'] = $html;
        }
        return $data;
    }

    public function getConfigData($path)
    {
        $value = $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $value;
    }
}
