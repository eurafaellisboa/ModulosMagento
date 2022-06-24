<?php

namespace Smartwave\Megamenu\Plugin;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class RemoveBlock implements ObserverInterface
{
    protected $_scopeConfig;

    public function __construct
    (
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->_scopeConfig = $scopeConfig;
    }

    public function execute(Observer $observer)
    {
        /** @var \Magento\Framework\View\Layout $layout */
        $layout = $observer->getLayout();
        $block = $layout->getBlock('catalog.topnav');  // here block reference name to remove

        if ($block) {
            $remove = $this->_scopeConfig->getValue('sw_megamenu/general/enable', ScopeInterface::SCOPE_STORE);
            if ($remove) {
                $layout->unsetElement('catalog.topnav');
            }
        }
    }
}
