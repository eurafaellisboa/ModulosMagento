<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category  BSS
 * @package   Bss_PreSelectShippingPayment
 * @author    Extension Team
 * @copyright Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace  Bss\PreSelectShippingPayment\Block;

use Magento\Store\Model\ScopeInterface;

class ConfigMenthod extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Bss\PreSelectShippingPayment\Model\CompositeConfigProvider
     */
    protected $shippingPayment;

    /**
     * ConfigMenthod constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Bss\PreSelectShippingPayment\Model\CompositeConfigProvider $shippingPayment
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Bss\PreSelectShippingPayment\Model\CompositeConfigProvider $shippingPayment,
        array $data = []
    ) {
        $this->shippingPayment = $shippingPayment;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed|string
     */
    public function getShippingDefault()
    {
        $isEnabledShipping =  $this->scopeConfig->isSetFlag(
            'preselectshippingpayment/shipping/enable',
            ScopeInterface::SCOPE_STORE
        );
        if (!$isEnabledShipping) {
            return '';
        }
        return $this->shippingPayment->getShipingConfig('default');
    }

    /**
     * @return mixed|string
     */
    public function getPaymentDefault()
    {
        $isEnabledPayment =  $this->scopeConfig->isSetFlag(
            'preselectshippingpayment/payment/enable',
            ScopeInterface::SCOPE_STORE
        );
        if (!$isEnabledPayment) {
            return '';
        }
        return $this->shippingPayment->getPaymentConfig('default');
    }

    /**
     * @return mixed|string
     */
    public function getPaymentPosition()
    {
        $isEnabledPayment =  $this->scopeConfig->isSetFlag(
            'preselectshippingpayment/payment/enable',
            ScopeInterface::SCOPE_STORE
        );
        if (!$isEnabledPayment) {
            return '';
        }
        return $this->shippingPayment->getPaymentConfig('position');
    }

    /**
     * @return mixed|string
     */
    public function getShippingPosition()
    {
        $isEnabledPayment =  $this->scopeConfig->isSetFlag(
            'preselectshippingpayment/payment/enable',
            ScopeInterface::SCOPE_STORE
        );
        if (!$isEnabledPayment) {
            return '';
        }
        return $this->shippingPayment->getShipingConfig('position');
    }
}
