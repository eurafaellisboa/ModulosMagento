<?php

/**
 *
 */

namespace Magento\ItauPix\Model;

use Magento\Backend\App\Action;
use Magento\Auth\OAuth;
use Magento\ItauPix\Api\Client;
use Magento\Core\Model\ModulesManagement;
use Magento\ItauPix\Api\Exceptions\ItauPixException;
use Magento\Framework\App\Config\ScopeConfigInterface;

class ItauPix
{

    protected $_cliente;

    public function __construct()
    {
        $this->_cliente = new Client($this->getCoreConfig('payment/itaupix/client_id'), $this->getCoreConfig('payment/itaupix/client_secret'));

        $this->_cliente->makeEnvironment('gw-app-key');
        //$this->_cliente->makeEnvironment($this->getCoreConfig('payment/itaupix/environment'));
    }

    public function setLog($msg)
    {
        $writer = 0;
        if ($this->getCoreConfig('payment/itaupixconfig/log')) {
            $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/itaupix.log');
            $logger = new \Zend_Log();
            $logger->addWriter($writer);

            $logger->info($msg);
        }
    }


    public function getCoreConfig($valor)
    {
        $scopeConfig = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\Config\ScopeConfigInterface');
        return $scopeConfig->getValue($valor, \Magento\Store\Model\ScopeInterface::SCOPE_STORES);
    }

    public function addPayPix($paymentInfo, $amount)
    {

        $info = $paymentInfo->getData('additional_information');
        $order = $paymentInfo->getOrder();

        $itaupixpix_cpf_cnpj = preg_replace("/[^0-9]/", "", (isset($info['itaupixpix_cpf']) ? $info['itaupixpix_cpf'] : $order->getBillingAddress()->getVatId()));
       

        try {

            $incrementId = 'Pedido #' . $order->getIncrementId() . ' | ';
            $soft_descriptor = $incrementId . $this->getCoreConfig('payment/itaupix/soft_descriptor');
            $chave = $this->getCoreConfig('payment/itaupix/chave_pix');

            $docTypeCode = 'cpf';
            if (strlen($itaupixpix_cpf_cnpj) > 13) {
                $docTypeCode = 'cnpj';
            }

            $arrayPix = [
                "calendario" => [
                    "expiracao" => "36000"
                ],
                "devedor" => [
                    "nome" => $this->_removerCaracter($order->getBillingAddress()->getFirstName() . ' ' . $order->getBillingAddress()->getLastName()),
                ],
                "valor" => [
                    "original" => $amount
                ],
                "chave" => $chave,
                "solicitacaoPagador" => substr((string)($soft_descriptor ? $soft_descriptor : '***'), 0, 200)
            ];

            $arrayPix["devedor"][$docTypeCode] = $itaupixpix_cpf_cnpj;

            $transaction = $this->_cliente->makePix($arrayPix);

            if (isset($transaction->txid) && !empty($transaction->txid)) {
                $paymentInfo->setAdditionalInformation('itaupix_transactions_txid', $transaction->txid);
                $paymentInfo->setAdditionalInformation('itaupix_acquirer_chave', $transaction->chave);
                $paymentInfo->setAdditionalInformation('itaupix_pix_qr_code', $transaction->pixCopiaECola);
            } else {
                $msg = 'Erro de conexão com o Itau';

                if (!empty($transaction->detail)) {
                    $msg = $transaction->detail;
                }

                throw new \Magento\Framework\Exception\LocalizedException(__($msg));
            }

            $this->setLog(json_encode($transaction));
        } catch (Exception $e) {
            $this->setLog(json_encode($e->getMessage()));
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        }
        return $transaction;
    }

    public function getPaymentAction()
    {
        return  $this->getCoreConfig('payment/itaupixcc/payment_action_type');
    }

    public function _removerCaracter($string)
    {
        return preg_replace(array("/(ç|Ç)/", "/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/"), explode(" ", "c a A e E i I o O u U n N"), $string);
    }

    public function cancelPymentItauPix($id_itaupix)
    {
        try {
            $this->_cliente->transactions()->refund([
                'id' => $id_itaupix,
            ]);
        } catch (Exception $e) {
            $this->setLog(json_encode($e->getMessage()));
        }
    }

    public function capturedTransaction($id_itaupix, $amount)
    {
        try {
            $this->_cliente->transactions()->capture([
                'id' => $id_itaupix,
                'amount' => str_replace(".", "", number_format((float)$amount, 2, '.', ''))
            ]);
        } catch (Exception $e) {
            $this->setLog(json_encode($e->getMessage()));
        }
    }


    public function consultPymentItauPix($txid)
    {
        try {
            $transaction = json_decode($this->_cliente->consultingStatus($txid));
            return  $transaction->status;
        } catch (Exception $e) {
            $this->setLog(json_encode($e->getMessage()));
        }
    }



    public function baseUrlLoja()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        return $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
    }

    public function buscarUf($uf)
    {
        $idUf = 'SP';
        switch ($uf) {
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

    public function cleanInfoTable($quote_id)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $sql = "UPDATE " . $resource->getTableName('quote_payment') . " SET additional_information = '' WHERE quote_id = " . $quote_id;
        $connection->query($sql);
    }
}
