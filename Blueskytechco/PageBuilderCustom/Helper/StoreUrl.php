<?php

namespace Blueskytechco\PageBuilderCustom\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class StoreUrl extends AbstractHelper
{
   
    protected $_storeManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
    }

    public function getStoreLinkUrl($url)
    {
        if (!$url || $url == '') {
            return null;
        }

        return $this->_urlBuilder->getUrl(null, ['_direct' => $url]);
    }
}
