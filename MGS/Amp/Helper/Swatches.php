<?php
namespace MGS\Amp\Helper;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;

class Swatches extends \Magento\Framework\App\Helper\AbstractHelper {
	
	/**
     * @var \Magento\Swatches\Helper\Data
     */
    protected $_swatchesHelper;
	
	/**
     * @param \Magento\Swatches\Helper\Data    	$swatchesHelper
     */
	public function __construct(
		\Magento\Swatches\Helper\Data $swatchesHelper
	) {
		$this->_swatchesHelper = $swatchesHelper;
	}
	
	/**
     * Check if an attribute is Swatch
     *
     * @param Attribute $attribute
     * @return bool
     */
    public function isSwatchAttribute(Attribute $attribute) {
        return $this->_swatchesHelper->isSwatchAttribute($attribute);
    }
}