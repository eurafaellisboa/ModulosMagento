<?php

namespace Blueskytechco\Themeoption\Block\Html;

class Footer extends \Magento\Framework\View\Element\Template
{
    public function getConfig($config_path)
    {
        return $this->_scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getSettingCmsBlock()
    {
        return $this->getConfig('themesetting/footer/select_footer_type');
    }

    public function enableMenuMobile()
    {
        return $this->getConfig('themesetting/footer/menu_mobile');
    }

    public function enableBackToTop()
    {
        if($this->getConfig('themesetting/general/back_to_top_enabled') == 'enable'){
            return true;
        }
        return false;
    }

    public function _toHtml()
    {
        $this->setTemplate('Magento_Theme::html/footer_custom.phtml');
        return parent::_toHtml();
    }
}
