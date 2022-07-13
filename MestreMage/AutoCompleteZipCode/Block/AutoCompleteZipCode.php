<?php


namespace MestreMage\AutoCompleteZipCode\Block;

class AutoCompleteZipCode extends \Magento\Framework\View\Element\Template
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
        if($this->getLocale()) {
            return $this->getCoreConfig('autocompletezipcode/geral/enabled');
        }else{
            return false;
        }
    }
    /**
     * @return string
     */

    public function getCoreConfig($line){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $scopeConfig = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        $value = $scopeConfig->getValue($line, $storeScope);

        if(!$value) {
            if($scopeConfig->getValue("autocompletezipcode/geral/log", $storeScope)){
                $this->setLog('Faltou configurações do modulo no painel  ex: street 1');
            }
        }

        return $value;
    }

    public function getLocale(){
        $info = $this->getCoreConfig('general/locale/code');

        if($info == 'pt_BR'){
            return true;
        }else{
            if($this->getCoreConfig("autocompletezipcode/geral/log")){
                $this->setLog('o idioma da loja não esta em português  | pt_BR');
            }
            return false;
        }

    }

    public function setLog($msg){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/AutoCompleteZipCode.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $logger->info($msg);
    }

}
