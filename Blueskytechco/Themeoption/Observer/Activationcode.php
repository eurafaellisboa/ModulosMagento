<?php

namespace Blueskytechco\Themeoption\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\PageCache\Version;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;

class Activationcode implements ObserverInterface
{
    protected $_messageManager;
    protected $cacheTypeList;
    protected $cacheFrontendPool;

    private $_verifypurchasecode;

    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        TypeListInterface $cacheTypeList, 
        Pool $cacheFrontendPool,
        \Blueskytechco\Themeoption\Helper\Verifypurchasecode $verifypurchasecode
    ) {
        $this->_messageManager = $messageManager;
        $this->_verifypurchasecode = $verifypurchasecode;
        $this->cacheTypeList = $cacheTypeList;
        $this->cacheFrontendPool = $cacheFrontendPool;
    }

    /**
     * Log out user and redirect to new admin custom url
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.ExitExpression)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $verify_envato_purchase_code = $this->_verifypurchasecode->verifyEnvatoPurchaseCode();
        if(is_array($verify_envato_purchase_code)){
            $_types = [
                'config',
                'layout',
                'block_html',
                'full_page'
            ];

            foreach ($_types as $type) {
                $this->cacheTypeList->cleanType($type);
            }
            foreach ($this->cacheFrontendPool as $cacheFrontend) {
                $cacheFrontend->getBackend()->clean();
            }
            if(isset($verify_envato_purchase_code['result']) && $verify_envato_purchase_code['result'] == 'success'){
                $this->_messageManager->getMessages(true);
                $this->_messageManager->addSuccess(__('Theme successfully activated using manual activation. Thanks for buying our theme.'));
            }
            elseif(isset($verify_envato_purchase_code['msg'])){
                $this->_messageManager->getMessages(true);
                $this->_messageManager->addError(__($verify_envato_purchase_code['msg']));
            }
        }
        elseif($verify_envato_purchase_code == 5){
            $this->_messageManager->getMessages(true);
            $this->_messageManager->addSuccess(__('You are using localhost, so do not need use purchase code.'));
        }
    }
}
