<?php
namespace Blueskytechco\Themeoption\Block\System\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Resetthemedesign extends Field
{
    protected $_template = 'Blueskytechco_Themeoption::system/config/form/field/resetthemedesign.phtml';
	
	public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }
	
	public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }
	
	protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }
	
	public function getAjaxUrl()
    {
        return $this->getUrl('themeoption/system_config/resetthemedesign');
    }
	
	public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'id' => 'resetthemedesign_button',
                'label' => __('Reset Now'),
            ]
        );
 
        return $button->toHtml();
    }
}