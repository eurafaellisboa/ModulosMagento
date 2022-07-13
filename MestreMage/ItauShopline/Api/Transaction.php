<?php
/**
 *
 * @author      Jamacio Rocha
 * @copyright   2018 MestreMage (https://mestremage.com.br)
 * @license     https://mestremage.com.br Copyright
 *
 * @link        https://mestremage.com.br/
 */

namespace MestreMage\ItauShopline\Api;


class Transaction
{

    public function addPayItauShopline($payment, $amount){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $info = $payment->getData('additional_information');
        $order = $payment->getOrder();
        $shipping_method = $order->getShippingMethod();
        $increment_id = $order->getIncrementId();
        $customer_email = $order->getCustomerEmail();
        $shipping_amount = $order->getShippingAmount();
        $discount_amount = $order->getDiscountAmount();
        $customer_name = $order->getBillingAddress()->getFirstName().' '.$order->getBillingAddress()->getLastName();
        $pag_itaushopline_taxvat = $info['pag_itaushopline_taxvat'];
        $street = $order->getBillingAddress()->getStreet();
        if(count($street) == 4){
            $rua = (isset($street[0]) ? $street[0] : '..');
            $complemento = (isset($street[2]) ? $street[2] : '..');
            $numero = (isset($street[1]) ? $street[1] : '..');
            $bairro = (isset($street[3]) ? $street[3] : '--');
        }else{
            $rua = (isset($street[0]) ? $street[0] : '..');
            $complemento = '';
            $numero = (isset($street[1]) ? $street[1] : '..');
            $bairro = (isset($street[2]) ? $street[2] : '--');
        }
        $itemOrderm = array();
        $orderItems = $order->getAllItems();
        $count = 0;
        foreach ($orderItems as $item) {
            $itemOrderm[$count]['quantity'] = $item->getQtyOrdered();
            $itemOrderm[$count]['item_id'] = $item->getSku();
            $itemOrderm[$count]['price_cents'] = str_replace(".", "", number_format((float)$item->getPrice(), 2, '.', ''));
            $itemOrderm[$count]['description'] = $item->getName();
            $count++;
        }
        $data = array(
            'apiKey' => $this->storeConfig('payment/itaushopline/api_key'),
            'order_id' => $increment_id, // código interno do lojista para identificar a transacao.
            'payer_email' => $customer_email,
            'payer_name' => $customer_name, // nome completo ou razao social
            'payer_cpf_cnpj' => $pag_itaushopline_taxvat, // cpf ou cnpj
            'payer_phone' => $order->getBillingAddress()->getTelephone(), // fixou ou móvel
            'payer_street' => $rua,
            'payer_number' => $numero,
            'payer_complement' => $complemento,
            'payer_district' => $bairro,
            'payer_city' => $order->getBillingAddress()->getCity(),
            'payer_state' => $order->getBillingAddress()->getRegion(), // apenas sigla do estado
            'payer_zip_code' => $order->getBillingAddress()->getPostcode(),
            'notification_url' => $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB).'statusItauShopline',
            'discount_cents' => str_replace(".", "", number_format((float)abs($discount_amount), 2, '.', '')), // em centavos
            'shipping_price_cents' => str_replace(".", "", number_format((float)$shipping_amount, 2, '.', '')), // em centavos
            'shipping_methods' => $shipping_method,
            'fixed_description' => true,
            'type_bank_slip' => 'boletoA4', // formato do boleto
            'days_due_date' => $this->storeConfig('payment/itaushopline/days_due_date'), // dias para vencimento do boleto
            'late_payment_fine' => $this->storeConfig('payment/itaushopline/late_payment_fine'),// Percentual de multa após vencimento.
            'per_day_interest' => $this->storeConfig('payment/itaushopline/per_day_interest'), // Juros após vencimento.
            'items' => $itemOrderm,
        );

        return $this->createBoleto($data);
    }

    public function createBoleto($data){
        $data_post = json_encode( $data );
        $url = "http://api.itaushopline.com/transaction/create/";
        $mediaType = "application/json"; // formato da requisição
        $charSet = "UTF-8";
        $headers = array();
        $headers[] = "Accept: ".$mediaType;
        $headers[] = "Accept-Charset: ".$charSet;
        $headers[] = "Accept-Encoding: ".$mediaType;
        $headers[] = "Content-Type: ".$mediaType.";charset=".$charSet;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_post);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        $json = json_decode($result, true);
// captura o http code
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if($httpCode == 201) {
// CÓDIGO 201 SIGNIFICA QUE O BOLETO FOI GERADO COM SUCESSO

// Exemplo de como capturar a resposta json
            $retornoSucess = array();
            $retornoSucess['transaction_id'] = $json['create_request']['transaction_id'];
            $retornoSucess['url_slip'] = $json['create_request']['bank_slip']['url_slip'];
            $retornoSucess['digitable_line'] = $json['create_request']['bank_slip']['digitable_line'];
            $retornoSucess['status'] = $httpCode;
        } else {
            if($this->storeConfig('payment/itaushopline/log')) {
                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pag_hiper.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $logger->info(json_encode($json));
            }
            $retornoSucess['status'] = $httpCode;
            $retornoSucess['content'] = $json;

        }
        return $retornoSucess;
    }
    public function cancelBoleto($data){
        $data = array(
            'apiKey' => $this->storeConfig('payment/itaushopline/api_key'),
            'token' => $this->storeConfig('payment/itaushopline/token'),
            'status' => 'canceled',
            'transaction_id' => $data,
        );
        $data_post = json_encode( $data );
        $url = "https://api.itaushopline.com/transaction/cancel/";
//Configuracao do cabecalho da requisicao
        $mediaType = "application/json";
        $charSet = "UTF-8";
        $headers = array();
        $headers[] = "Accept: ".$mediaType;
        $headers[] = "Accept-Charset: ".$charSet;
        $headers[] = "Accept-Encoding: ".$mediaType;
        $headers[] = "Content-Type: ".$mediaType.";charset=".$charSet;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_post);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        $json = json_decode($result, true);
### captura o http code
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if($httpCode == 201): // CÓDIGO 201 SIGNIFICA QUE O BOLETO FOI GERADO COM SUCESSO
            return $json['cancellation_request']['response_message']; // Exemplo de como capturar a resposta json
        else:
            if($this->storeConfig('payment/itaushopline/log')) {
                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pag_hiper.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $logger->info(json_encode($json));
            }
            return $json['cancellation_request']['response_message']; // Exemplo de como capturar a resposta json
        endif;

    }
    public function storeConfig($code){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $scopeConfig = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        return $scopeConfig->getValue($code, $storeScope);
    }
}