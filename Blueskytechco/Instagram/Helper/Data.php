<?php

namespace Blueskytechco\Instagram\Helper;

use Magento\Framework\UrlInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    
    public function getConfig($key, $storeId = null)
    {
        $result = $this->scopeConfig->getValue($key, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        return $result;
    }

    public function getMediaPath() {
        return $this->_urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]);
    }
}