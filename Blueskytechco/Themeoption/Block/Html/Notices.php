<?php
namespace Blueskytechco\Themeoption\Block\Html;

class Notices extends \Magento\Framework\View\Element\Template
{
    protected $_verifypurchasecode;

    public function __construct(
        \Blueskytechco\Themeoption\Helper\Verifypurchasecode $verifypurchasecode,
        \Magento\Framework\View\Element\Template\Context $context, 
        array $data = []
    )
    {
        $this->_verifypurchasecode = $verifypurchasecode;
        parent::__construct($context, $data);
    }

    public function getConfig($config_path)
    {
        return $this->_scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getCmsBlockTopHeader()
    {
        return $this->getConfig('themesetting/header/add_section_to_top_header');
    }

    public function displayNoticeActivationPurchaseCode()
    {
        return !$this->_verifypurchasecode->checkEnvatoPurchaseCode();;
    }
}
