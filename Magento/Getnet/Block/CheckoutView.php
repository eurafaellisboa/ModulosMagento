<?php


namespace Magento\Getnet\Block;

class CheckoutView extends \Magento\Framework\View\Element\Template
{

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function isActiv(){
//        if($this->getLocale()) {
//            return $this->getCoreConfig('autocompletezipcode/geral/ativarmodulo');
//        }else{
//            return false;
//        }
    }
    /**
     * @return string
     */

//    public function getCoreConfig($line){
//        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
//        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
//        $scopeConfig = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
//        $value = $scopeConfig->getValue($line, $storeScope);
//        if($value) {
//            return $value;
//        }else{
//            if($this->getCoreConfig("autocompletezipcode/loja/log")){
//                $this->setLog('Faltou configurações do modulo no painel  ex: street 1');
//            }
//            return false;
//        }
//    }


    public function setLog($msg){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/getnet.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $logger->info($msg);
    }

}
