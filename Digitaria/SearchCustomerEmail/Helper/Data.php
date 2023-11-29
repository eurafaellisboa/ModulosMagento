<?php
namespace Digitaria\SearchCustomerEmail\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const XML_PATH_MASK_ENABLED = 'searchcustomeremail/general/mask_enabled';
	 const XML_PATH_CONTENT = 'searchcustomeremail/general/content';

    /**
     * Check if the mask is enabled
     *
     * @return bool
     */
    public function isMaskEnabled()
    {
        return (bool) $this->scopeConfig->getValue(
            self::XML_PATH_MASK_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }
	
	public function pageContent()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CONTENT,
            ScopeInterface::SCOPE_STORE
        );
    }
}
