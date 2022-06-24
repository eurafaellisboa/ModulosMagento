<?php
namespace Blueskytechco\Instagram\Block\Adminhtml\Button;

class Fetchposts extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $_indata;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Blueskytechco\Instagram\Helper\Data $indata,
        array $data = []
    ) {
        $this->_indata = $indata;
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $buttonBlock = $this->getForm()->getLayout()->createBlock('Magento\Backend\Block\Widget\Button');

        $params = [
            'store' => $buttonBlock->getRequest()->getParam('store')
        ];

        $url = $this->getUrl("instagram/fetchposts/submitapi", $params);
        $data = [
            'id' => 'fetchposts' ,
            'label' => __('Submit'),
            'onclick' => "setLocation('" . $url . "')"
        ];

        $userid = $this->_indata->getConfig('instagramsection/instagramgroup/userid');
        $accesstoken = $this->_indata->getConfig('instagramsection/instagramgroup/accesstoken');
        $username = $this->_indata->getConfig('instagramsection/instagramgroup/username');

        if($userid == '' || $accesstoken == '' || $username == ''){
            $data['disabled'] = 'disabled';
        }

        $html = $buttonBlock->setData($data)->toHtml();
        return $html;
    }
}