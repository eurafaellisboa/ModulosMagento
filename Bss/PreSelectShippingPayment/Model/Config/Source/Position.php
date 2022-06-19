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

/**
 * Class Position
 *
 * @package Bss\PreSelectShippingPayment\Model\Config\Source
 */
class Position implements ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('None'),
                'value' => 'none'
            ],
            [
                'label' => __('Last Method'),
                'value' => 'last'
            ],
            [
                'label' => __('First Method'),
                'value' => 'first'
            ]
        ];
    }
}
