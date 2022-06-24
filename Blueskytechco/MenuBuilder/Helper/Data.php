<?php
namespace Blueskytechco\MenuBuilder\Helper;

use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\View\Helper\SecureHtmlRenderer;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    
    /**
     * @var SecureHtmlRenderer
     */
    protected $secureRenderer;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        SecureHtmlRenderer $secureRenderer,
        Registry $registry
    ) {
        $this->_resource = $resource;
        $this->_registry = $registry;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }
    
    /**
     * Return config data
     *
     * @return string
     */
    public function getData($config)
    {
        $config = $this->scopeConfig->getValue($config, ScopeInterface::SCOPE_STORE);
        return $config;
    }
}
