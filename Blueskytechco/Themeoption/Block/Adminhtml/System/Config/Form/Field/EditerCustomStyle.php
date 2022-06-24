<?php
namespace Blueskytechco\Themeoption\Block\Adminhtml\System\Config\Form\Field;

class EditerCustomStyle extends \Magento\Backend\Block\AbstractBlock implements
    \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
   
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return sprintf(
            '<tr class="system-fieldset-editer" id="row_%s"><td colspan="5"><div id="editor-custom-style-css" style="display: none; margin-top: 20px; height: 400px; border: 1px solid #bbbbbb;"></div></td></tr>',
            $element->getHtmlId(),
            $element->getHtmlId(),
            $element->getLabel()
        );
    }
}
