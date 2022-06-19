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

namespace Bss\PreSelectShippingPayment\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Payment\Model\Config;

/**
 * Class PaymentMethods
 *
 * @package Bss\PreSelectShippingPayment\Model\Config\Source
 */
class PaymentMethods implements ArrayInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected $appConfigScopeConfigInterface;

    /**
     * @var Config
     */
    protected $paymentModelConfig;

    /**
     * @var ScopeConfigInterface
     */
    protected $scope;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scope,
        ScopeConfigInterface $appConfigScopeConfigInterface,
        Config $paymentModelConfig
    ) {
        $this->scope = $scope;
        $this->appConfigScopeConfigInterface = $appConfigScopeConfigInterface;
        $this->paymentModelConfig = $paymentModelConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $payments = $this->paymentModelConfig->getActiveMethods();
        $methods = [];
        foreach ($payments as $paymentCode => $paymentModel) {
            $paymentTitle = $this->appConfigScopeConfigInterface
                ->getValue('payment/'.$paymentCode.'/title');
            $methods[$paymentCode] = [
                'label' => $paymentTitle,
                'value' => $paymentCode
            ];
        }
        return $methods;
    }
}
