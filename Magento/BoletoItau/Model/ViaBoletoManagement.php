<?php
declare(strict_types=1);

namespace Magento\BoletoItau\Model;

use Magento\Framework\UrlInterface;
use \Magento\Sales\Model\Order;
use \Magento\Framework\Exception\LocalizedException;
use \Magento\Sales\Model\Order\Payment;
use \Magento\BoletoItau\Api\Itaucripto;

class ViaBoletoManagement implements \Magento\BoletoItau\Api\ViaBoletoManagementInterface
{

    /**
     * {@inheritdoc}
     */
    public function getViaBoleto($param)
    {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $objectManager->create('\Magento\Sales\Model\Order')->load($param);
    
        $payment = $order->getPayment();

        $info = $payment->getData('additional_information');
        $order = $payment->getOrder();
        $amount = $order->getData('grand_total');
        $street = $order->getBillingAddress()->getStreet();
        if(count($street) == 4){
            $rua = (isset($street[0]) ? $street[0] : '..');
            $complemento = (isset($street[2]) ? $street[2] : '..');
            $numero = (isset($street[1]) ? $street[1] : '..');
            $bairro = (isset($street[3]) ? $street[3] : '..');
        }else{
            $rua = (isset($street[0]) ? $street[0] : '..');
            $complemento = '';
            $numero = (isset($street[1]) ? $street[1] : '..');
            $bairro = (isset($street[2]) ? $street[2] : '..');
        }

        $boletoitau_taxvat = (isset($info['boletoitau_taxvat']) ? $info['boletoitau_taxvat'] : $order->getBillingAddress()->getVatId());

        if(empty($boletoitau_taxvat)) {
            throw new \Magento\Framework\Exception\LocalizedException(__('CPF obrigatorio!'));
        }
        $draweeDocNumber = preg_replace("/[^0-9]/","",($boletoitau_taxvat));

        $draweeDocTypeCode = '01';
        if(strlen($draweeDocNumber) > 13){
            $draweeDocTypeCode = '02';
        }

        $order_number = substr(number_format(time() * rand(),0,'',''),0,8);
        $bankSlipDueDate       = date('dmY', strtotime('+'.(int)$this->storeConfig('payment/boletoitau/days_due_date').' day'));
        $itaucripto = new Itaucripto();
        $itaucripto->setCompanyCode(strtoupper($this->storeConfig('payment/boletoitau/company_code')));
        $itaucripto->setEncryptionKey(strtoupper($this->storeConfig('payment/boletoitau/encrytion_Key')));
        $itaucripto->setOrderNumber($order_number);
        $itaucripto->setAmount(str_replace('.',',',number_format((float)$amount, 2, '.', '')));
        $itaucripto->setDraweeName(substr($order->getBillingAddress()->getFirstName().' '.$order->getBillingAddress()->getLastName(), 0, 30));
        $itaucripto->setDraweeDocTypeCode($draweeDocTypeCode);
        $itaucripto->setDraweeDocNumber($draweeDocNumber);
        $itaucripto->setDraweeAddress(substr($rua.','.$numero.' '.$complemento, 0, 40));
        $itaucripto->setDraweeAddressDistrict(substr($bairro, 0, 15));
        $itaucripto->setDraweeAddressCity(substr($order->getBillingAddress()->getCity(), 0, 15));
        $itaucripto->setDraweeAddressState($this->buscarUf($order->getBillingAddress()->getRegionId()));
        $itaucripto->setDraweeAddressZipCode(substr(preg_replace("/[^0-9]/", "",$order->getBillingAddress()->getPostcode()), 0, 8));
        $itaucripto->setBankSlipDueDate($bankSlipDueDate);
        $itaucripto->setBankSlipNoteLine1($this->storeConfig('payment/boletoitau/bank_slip_noteline1'));
        $itaucripto->setBankSlipNoteLine2($this->storeConfig('payment/boletoitau/bank_slip_noteline2'));
        $itaucripto->setBankSlipNoteLine3($this->storeConfig('payment/boletoitau/bank_slip_noteline3'));
        $itaucripto->setNote(3);
        $itaucripto->setCallbackUrl('/callback');
        $dataGenerate = $itaucripto->generateData();
        $dataQuery    = $itaucripto->generateQuery(0);

        if($dataGenerate){
            $payment->setAdditionalInformation('itau_shopline_data_genereted', $dataGenerate);
            $payment->setAdditionalInformation('itau_shopline_data_query', $dataQuery);
            $payment->setAdditionalInformation('itau_shopline_valor', (float)$amount);
            $payment->setAdditionalInformation('order_number_itau_shopline', $order_number);
            $payment->save();
            return 'Segunda via Gerada com sucesso';
        }else{
           return 'Erro ao tentar gerar os dados';
        }

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

    public function storeConfig($code){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $scopeConfig = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        return $scopeConfig->getValue($code, $storeScope);
    }
}