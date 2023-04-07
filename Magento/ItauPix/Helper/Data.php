<?php
/**
 *
 */

namespace Magento\ItauPix\Helper;
use Magento\Backend\App\Action;
use Magento\Auth\OAuth;
use Magento\ItauPix\Api\Client;



class Data extends \Magento\Framework\App\Helper\AbstractHelper {


    protected $_scopeConfig;
    protected $tokenauth;
    protected $keyauth;
    protected $_objectManager;
    protected $date;

    protected $merchant;
    protected $environment;

    protected $typediscountparcel;
    protected $valuediscountparcel;
    protected $textdiscountparcel;


    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor
    )
    {
        $this->_scopeConfig = $scopeConfig;
        $this->_objectManager = $objectManager;
        $this->date = $date;
        $this->_storeManager = $storeManager;
        $this->_logger = $logger;
        $this->_resource = $resource->getConnection();
        $this->_customerSession = $customerSession;
        $this->encryptor = $encryptor;
        $this->_verifycarcredit = [];

    }


    public function getPaymentAction(){
        return $this->_scopeConfig->getValue('payment/itaupixcc/payment_action_type', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

    }

    public function setLog($msg){
        $writer = 0;
        if($this->_scopeConfig->getValue('payment/magentonfig/log', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/itaupix_card.log');
            $logger = new \Zend_Log();
            $logger->addWriter($writer);

            $logger->info($msg);
        }
    }



    public function getDateDue($NDias)
    {
        $date = $this->date->gmtDate('Y-m-d', strtotime("+{$NDias} days"));

        return  $date;
    }

    public function getJurosSimples($valor, $juros, $parcela)
    {
        $principal = $valor;
        $taxa = $juros/100;
        $valjuros = $principal * $taxa;
        $valParcela = ($principal + $valjuros)/$parcela;
        return $valParcela;
    }
    public function buscarUf($uf){
        $idUf = '';
        switch($uf) {
            case 485:
                $idUf = "AC";
                break;
            case 486:
                $idUf = "AL";
                break;
            case 487:
                $idUf = "AP";
                break;
            case 488:
                $idUf = "AM";
                break;
            case 489:
                $idUf = "BA";
                break;
            case 490:
                $idUf = "CE";
                break;
            case 491:
                $idUf = "ES";
                break;
            case 492:
                $idUf = "GO";
                break;
            case 493:
                $idUf = "MA";
                break;
            case 494:
                $idUf = "MT";
                break;
            case 495:
                $idUf = "MS";
                break;
            case 496:
                $idUf = "MG";
                break;
            case 497:
                $idUf = "PA";
                break;
            case 498:
                $idUf = "PB";
                break;
            case 499:
                $idUf = "PR";
                break;
            case 500:
                $idUf = "PE";
                break;
            case 501:
                $idUf = "PI";
                break;
            case 502:
                $idUf = "RJ";
                break;
            case 503:
                $idUf = "RN";
                break;
            case 504:
                $idUf = "RS";
                break;
            case 505:
                $idUf = "RO";
                break;
            case 506:
                $idUf = "RR";
                break;
            case 507:
                $idUf = "SC";
                break;
            case 508:
                $idUf = "SP";
                break;
            case 509:
                $idUf = "SE";
                break;
            case 510:
                $idUf = "TO";
                break;
            case 511:
                $idUf = "DF";
                break;
        }
        return $idUf;
    }
    public function getJurosComposto($valor, $juros, $parcela)
    {
        $principal = $valor;
        $taxa = $juros/100;
        $valParcela = ($principal * $taxa) / (1 - (pow(1 / (1 + $taxa), $parcela)));
        return $valParcela;
    }


}