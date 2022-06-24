<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace MGS\AjaxCart\Model\Source;

/**
 * Class DisplayFor
 * @package Aheadworks\Ajaxcartpro\Model\Source
 */
class DisplayFor implements \Magento\Framework\Option\ArrayInterface
{
    /**#@+
     * "Display for" types
     */
    const PRODUCTS_WITH_REQUIRED_OPTIONS = 0;

    const PRODUCTS_WITH_ANY_OPTIONS = 1;

    const PRODUCTS_ALL = 2;
    /**#@-*/

    /**
     * @var null|array
     */
    private $optionArray;

    /**
     * Get option array
     *
     * @return array
     */
    public function getOptions()
    {
        return [
            self::PRODUCTS_WITH_REQUIRED_OPTIONS => __('Products with required options only'),
            self::PRODUCTS_WITH_ANY_OPTIONS => __('Products with any options'),
            self::PRODUCTS_ALL => __('All products'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        if (!$this->optionArray) {
            $this->optionArray = [];
            foreach ($this->getOptions() as $value => $label) {
                $this->optionArray[] = ['value' => $value, 'label' => $label];
            }
        }
        return $this->optionArray;
    }

    /**
     * Get label by value
     *
     * @param int $value
     * @return null|\Magento\Framework\Phrase
     */
    public function getOptionLabelByValue($value)
    {
        $options = $this->getOptions();
        if (array_key_exists($value, $options)) {
            return $options[$value];
        }
        return null;
    }
}
