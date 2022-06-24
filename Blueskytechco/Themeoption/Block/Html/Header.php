<?php

namespace Blueskytechco\Themeoption\Block\Html;

class Header extends \Magento\Framework\View\Element\Template
{

    public function getConfig($config_path)
    {
        return $this->_scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function isEnableTopBar()
    {
        return $this->getConfig('themesetting/header/enable_topbar');
    }

    public function _toHtml()
    {
        $header_config = $this->_scopeConfig->getValue(
            'themesetting/header/select_header_type',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if($header_config && $header_config != ''){
            $this->setTemplate('Magento_Theme::html/headers/'.$header_config.'.phtml');
        }
        else{
            $this->setTemplate('Magento_Theme::html/header_custom.phtml');
        }
        
        return parent::_toHtml();
    }
}
