<?php

namespace Hiddentechies\Neworder\Helper;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Helper\View as CustomerViewHelper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    /**
     * Neworder form data
     */
    protected $_data;

    /**
     * Initialize
     *
     * @param Magento\Framework\App\Helper\Context $context
     * @param Magento\Catalog\Model\ProductFactory $productFactory
     * @param Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * Fetch System Config Value
     */
    public function getConfigVal($str = '') {
        return $this->scopeConfig
                        ->getValue($str, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

}