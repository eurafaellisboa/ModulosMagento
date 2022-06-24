<?php

namespace Blueskytechco\Instagram\Model\Source;

class Resolution implements \Magento\Framework\Option\ArrayInterface
{

    protected $_options;

    public function toOptionArray() {

        $this->_options = [
            ['label' => '150px x 150px', 'value' => 'thumbnail'],
            ['label' => '306px x 306px', 'value' => 'low_resolution'],
            ['label' => '612px x 612px', 'value' => 'standard_resolution'],
        ];
        return $this->_options;
    }

}