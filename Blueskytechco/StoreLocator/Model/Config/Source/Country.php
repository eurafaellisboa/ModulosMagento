<?php
/**
 * Copyright © 2019 Blueskytechco. All rights reserved.
 */

namespace Blueskytechco\StoreLocator\Model\Config\Source;

use \Magento\Framework\Option\ArrayInterface;
use \Magento\Directory\Model\ResourceModel\Country\Collection as CountryCollection;

class Country implements ArrayInterface
{

    private $countryCollection;


    public function __construct(CountryCollection $countryCollection)
    {
        $this->countryCollection = $countryCollection;
    }


    private $options;


    public function toOptionArray($isMultiselect = false, $foregroundCountries = '')
    {
        $optionsArr = [];
        if (!$this->options) {
            $this->options = $this->countryCollection->loadData()->setForegroundCountries(
                $foregroundCountries
            )->toOptionArray(
                false
            );
        }

        $options = $this->options;
        if (!$isMultiselect) {
            array_unshift($options, ['value' => '', 'label' => __('--Please Select--')]);
        }

        foreach ($options as $option) {
            $optionsArr[$option['value']] = $option['label'];
        }

        return $optionsArr;
    }
}
