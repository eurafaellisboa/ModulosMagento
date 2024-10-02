<?php

namespace Digitaria\RDMarketing\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Backend\Model\UrlInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Button extends Field
{
    protected $_backendUrl;

    public function __construct(
        Context $context,
        UrlInterface $backendUrl,
        array $data = []
    ) {
        $this->_backendUrl = $backendUrl;
        parent::__construct($context, $data);
    }

    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    public function getCustomUrl()
    {
        // Use $this->_backendUrl->getUrl para criar a URL
        return $this->_backendUrl->getUrl('rdmarketing/generate/result');
    }
}
