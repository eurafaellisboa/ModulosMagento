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
use Magento\Shipping\Model\Config\Source\Allmethods;

/**
 * Class ShippingMethods
 *
 * @package Bss\PreSelectShippingPayment\Model\Config\Source
 */
class ShippingMethods implements ArrayInterface
{
    /**
     * @var \Magento\Shipping\Model\Config\Source\Allmethods
     */
    private $allShippingMethods;

    /**
     * @param \Magento\Shipping\Model\Config\Source\Allmethods $allShippingMethods
     */
    public function __construct(
        Allmethods $allShippingMethods
    ) {
        $this->allShippingMethods = $allShippingMethods;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $results = $this->allShippingMethods->toOptionArray(true);
        if (isset($results[0]) && isset($results[0]['label'])) {
            $results[0]['label'] = __('-- Please select --');
        }
        return $results;
    }
}
