<?php

namespace Nwdthemes\Revslider\Model\Config\Source;

use \Nwdthemes\Revslider\Helper\Framework;
use \Nwdthemes\Revslider\Model\Revslider\RevSliderSlider;

class Revslider implements \Magento\Framework\Option\ArrayInterface {

    protected $_revSliderSlider;

	public function __construct(
        \Nwdthemes\Revslider\Helper\Framework $frameworkHelper
    ) {
        $this->_revSliderSlider = new RevSliderSlider($frameworkHelper);
	}

	public function toOptionArray() {
        $options = array();
		foreach ($this->_revSliderSlider->getArrSliders() as $slider) {
			$options[] = [
                'value' => $slider->getAlias(),
                'label' => $slider->getTitle()
            ];
		}
		return $options;
	}

    public function toArray() {
        $options = array();
		foreach ($this->_revSliderSlider->getArrSliders() as $slider) {
			$options[$slider->getAlias()] = $slider->getTitle();
		}
		return $options;
    }

}