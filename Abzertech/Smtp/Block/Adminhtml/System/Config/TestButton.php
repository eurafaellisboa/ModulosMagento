<?php

namespace Abzertech\Smtp\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Button;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class TestButton extends Field
{

    /**
     * @var urlBuilder
     */
    protected $urlBuilder;

    public function __construct(
        Context $context,
        array $data = []
    ) {
        $this->urlBuilder = $context->getUrlBuilder();
        parent::__construct($context, $data);
    }

    /**
     * TestButton constructor.
     *
     * @param Context $context
     * @param array $data
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('Abzertech_Smtp::system/config/testbutton.phtml');
    }

    /**
     * Generate button html
     *
     * @return string
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock(
            Button::class
        )->setData(
            [
                    'id' => 'abzertechsmtp_debug_result_button',
                    'label' => __('Send Test Email'),
                    'onclick' => 'javascript:abzertechSmtpAppDebugTest(); return false;',
                    ]
        );

        return $button->toHtml();
    }

    /**
     * Generate admin url
     *
     * @return string
     */
    public function getAdminUrl()
    {
        return $this->urlBuilder->getUrl(
            'abzertechsmtp/testemail',
            ['store' => $this->_request->getParam('store')]
        );
    }

    /**
     * Render button
     *
     * @param  AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Return element html
     *
     * @param  AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }
}
