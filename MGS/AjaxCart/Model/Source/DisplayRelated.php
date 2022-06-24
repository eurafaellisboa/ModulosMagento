<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace MGS\AjaxCart\Model\Source;

use Magento\Framework\Module\Manager as ModuleManager;

/**
 * Class DisplayRelated
 * @package Aheadworks\Ajaxcartpro\Model\Source
 */
class DisplayRelated implements \Magento\Framework\Option\ArrayInterface
{
    /**#@+
     * "Display related" types
     */
    const NATIVE_CROSS_SELLS = 'native_cross_sells';

    const ARP_BY_AHEADWORKS = 'aw_arp';

    const WBTAB_BY_AHEADWORKS = 'aw_wbtab';

    const NONE = 'none';
    /**#@-*/

    /**
     * @var ModuleManager
     */
    private $moduleManager;

    /**
     * @param ModuleManager $moduleManager
     */
    public function __construct(
        ModuleManager $moduleManager
    ) {
        $this->moduleManager = $moduleManager;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $optionArray = [];
        if ($this->moduleManager->isEnabled('Mgs_Autorelated')) {
            $optionArray[] = [
                'value' => self::ARP_BY_AHEADWORKS,
                'label' => __('Yes, Automatic Related Products by Aheadworks')
            ];
        }
        if ($this->moduleManager->isEnabled('Mgs_Wbtab')) {
            $optionArray[] = [
                'value' => self::WBTAB_BY_AHEADWORKS,
                'label' => __('Yes, Who Bought This Also Bought by Aheadworks')
            ];
        }
        $optionArray[] = ['value' => self::NATIVE_CROSS_SELLS, 'label' => __('Yes, Native Cross-Sells')];
        $optionArray[] = ['value' => self::NONE, 'label' => __('No')];
        return $optionArray;
    }
}
