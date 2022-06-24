<?php
namespace Blueskytechco\ProductWidgetAdvanced\Block\Adminhtml\Button;

class BestSeller extends \Magento\Config\Block\System\Config\Form\Field
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

        $url = $this->getUrl("productwidgetadvanced/collect/bestseller");
        $data = [
            'id' => 'best_seller' ,
            'label' => __('Run'),
            'onclick' => "setLocation('" . $url . "')"
        ];

        $html = $buttonBlock->setData($data)->toHtml();
        return $html;
    }
}