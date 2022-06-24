<?php
namespace Blueskytechco\QuickviewProduct\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Registry;

class QuickviewLayoutLoadBefore implements ObserverInterface
{

    protected $_registry;
    protected $_scopeConfig;
    protected $_pageResult;
    protected $request;
    protected $_productloader;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeInterface,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\View\Page\Config $pageConfig,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        Registry $registry
    ) {
        $this->_registry = $registry;
        $this->request = $request;
        $this->_pageConfig = $pageConfig;
        $this->_productloader = $_productloader;
        $this->_scopeConfig = $scopeInterface;
    }

    public function execute(Observer $observer)
    {
        $action = $observer->getData('full_action_name');
        
        if ($action != 'blueskytechco_quickview_product_view') {
            return $this;
        }
        $layout = $observer->getData('layout');
        /* Blueskytechco Quickview page */
        if ($action == 'blueskytechco_quickview_product_view') {
            $product_id = $this->request->getParam('id');

            if ($product_id) {
                $product = $this->_productloader->create()->load($product_id);
                if ($product) {
                    $productType = $product->getTypeId();
                    if ($productType == 'configurable') {
                        $layout->getUpdate()->addHandle('blueskytechco_quickview_product_view_configurable');
                    } elseif ($productType == 'simple') {
                        $layout->getUpdate()->addHandle('blueskytechco_quickview_product_view_simple');
                    } elseif ($productType == 'grouped') {
                        $layout->getUpdate()->addHandle('blueskytechco_quickview_product_view_grouped');
                    } elseif ($productType == 'downloadable') {
                        $layout->getUpdate()->addHandle('blueskytechco_quickview_product_view_downloadable');
                    } elseif ($productType == 'bundle') {
                        $layout->getUpdate()->addHandle('blueskytechco_quickview_product_view_bundle');
                    }
                }
            }
            return $this;
        }
        return $this;
    }
}
