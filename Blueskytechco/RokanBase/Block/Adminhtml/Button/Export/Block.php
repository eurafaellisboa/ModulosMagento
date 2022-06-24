<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Field renderer for PayPal merchant country selector
 */
namespace Blueskytechco\RokanBase\Block\Adminhtml\Button\Export;

use Magento\Paypal\Model\Config\StructurePlugin;

class Block extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Config path for merchant country selector
     */
    const FIELD_CONFIG_PATH = 'paypal/general/merchant_country';

    /**
     * Request parameter name for default country
     */
    const REQUEST_PARAM_DEFAULT_COUNTRY = 'paypal_default_country';

    /**
     * Country of default scope
     *
     * @var string
     */
    protected $_defaultCountry;

    /**
     * @var \Magento\Backend\Model\Url
     */
    protected $_url;

    /**
     * @var \Magento\Framework\View\Helper\Js
     */
    protected $_jsHelper;

    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $directoryHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Url $url
     * @param \Magento\Framework\View\Helper\Js $jsHelper
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Url $url,
        \Magento\Framework\View\Helper\Js $jsHelper,
        \Magento\Directory\Helper\Data $directoryHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_url = $url;
        $this->_jsHelper = $jsHelper;
        $this->directoryHelper = $directoryHelper;
    }

    /**
     * Get country selector html
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        /** @var \Magento\Backend\Block\Widget\Button $buttonBlock  */
        $buttonBlock = $this->getForm()->getLayout()->createBlock('Magento\Backend\Block\Widget\Button');

        $params = [
            'website' => $buttonBlock->getRequest()->getParam('website')
        ];

        $url = $this->getUrl("*/rokanbase/exportblock", $params);
        $data = [
            'id' => 'export_block' ,
            'label' => __('Export Blocks'),
            'onclick' => "setLocation('" . $url . "')",
        ];

        $html = $buttonBlock->setData($data)->toHtml();
        return $html;
    }
}
