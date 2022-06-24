<?php
namespace Blueskytechco\Themeoption\Block\Adminhtml\Button\Export;

class Configuration extends \Magento\Config\Block\System\Config\Form\Field
{
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $buttonBlock = $this->getForm()->getLayout()->createBlock('Magento\Backend\Block\Widget\Button');

        $params = [
            'store' => $buttonBlock->getRequest()->getParam('store')
        ];

        $url = $this->getUrl("themeoption/exportoptions/exportconfiguration", $params);
        $data = [
            'id' => 'export_configuration' ,
            'label' => __('Export'),
            'onclick' => "setLocation('" . $url . "')",
        ];

        $html = $buttonBlock->setData($data)->toHtml();
        return $html;
    }
}
