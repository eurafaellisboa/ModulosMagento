<?php
/**
 *
 * @author      Jamacio Rocha
 * @copyright   2018 MestreMage (https://mestremage.com.br)
 * @license     https://mestremage.com.br Copyright
 *
 * @link        https://mestremage.com.br/
 */

namespace MestreMage\PagarMe\Model;

use Magento\Backend\App\Action;
use MestreMage\Auth\OAuth;
use MestreMage\PagarMe\Api\Client;
use MestreMage\PagarMe\Api\Exceptions\PagarMeException;
use Magento\Framework\App\Config\ScopeConfigInterface;

class PagarMe
{

    protected $_cliente;

    public function __construct()
    {
        $this->_cliente = new Client($this->getCoreConfig('payment/pagarmeconfig/api_key'));
    }

    public function setLog($msg)
    {
        $writer = 0;
        if ($this->getCoreConfig('payment/pagarmeconfig/log')) {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pagarme.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);

            $logger->info($msg);
        }
    }


    public function getCoreConfig($valor)
    {
        $scopeConfig = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\Config\ScopeConfigInterface');
        return $scopeConfig->getValue($valor, \Magento\Store\Model\ScopeInterface::SCOPE_STORES);
    }

    public function addPayBoleto($paymentInfo, $amount)
    {
        $info = $paymentInfo->getData('additional_information');
        $order = $paymentInfo->getOrder();


        $pagarmebl_cpf = (isset($info['pagarmebl_cpf']) ? $info['pagarmebl_cpf'] : $order->getBillingAddress()->getVatId());

        if(!$pagarmebl_cpf){
            throw new \Magento\Framework\Exception\LocalizedException(__('Digite um CPF'));
        }

        $street_b = $order->getBillingAddress()->getStreet();

        if (count($street_b) == 4) {
            $rua_b = (isset($street_b[0]) ? $street_b[0] : '..');
            $complemento_b = (isset($street_b[2]) ? $street_b[2] : '..');
            $numero_b = (isset($street_b[1]) ? $street_b[1] : '..');
            $bairro_b = (isset($street_b[3]) ? $street_b[3] : '..');
        } else {
            $rua_b = (isset($street_b[0]) ? $street_b[0] : '..');
            $complemento_b = '';
            $numero_b = (isset($street_b[1]) ? $street_b[1] : '..');
            $bairro_b = (isset($street_b[2]) ? $street_b[2] : '..');
        }

        $street_s = $order->getShippingAddress()->getStreet();

        if (count($street_s) == 4) {
            $rua_s = (isset($street_s[0]) ? $street_s[0] : '..');
            $complemento_s = (isset($street_s[2]) ? $street_s[2] : '..');
            $numero_s = (isset($street_s[1]) ? $street_s[1] : '..');
            $bairro_s = (isset($street_s[3]) ? $street_s[3] : '..');
        } else {
            $rua_s = (isset($street_s[0]) ? $street_s[0] : '..');
            $complemento_s = '';
            $numero_s = (isset($street_s[1]) ? $street_s[1] : '..');
            $bairro_s = (isset($street_s[2]) ? $street_s[2] : '..');
        }

        $product_order = [];
        foreach ($order->getItems() as $key => $item) {
            $product_order[$key]['id'] = $item->getProductId();
            $product_order[$key]['title'] = $this->_removerCaracter($item->getSku());
            $product_order[$key]['unit_price'] = str_replace(".", "", number_format((float)$item->getPrice(), 2, '.', ''));
            $product_order[$key]['quantity'] = $item->getQtyOrdered();
            $product_order[$key]['tangible'] = true;
        }


        try {
            $array_input = [
                'amount' => str_replace(".", "", number_format((float)$amount, 2, '.', '')),
                "capture" => true,
                'installments' => 1,
                'payment_method' => 'boleto',
                'soft_descriptor' => $this->getCoreConfig('payment/pagarmebl/soft_descriptor'),
                'boleto_instructions' => $this->getCoreConfig('payment/pagarmebl/boleto_instructions'),
                'customer' => [
                    'external_id' => preg_replace("/[^0-9]/", "", $pagarmebl_cpf),
                    'name' => $this->_removerCaracter($order->getBillingAddress()->getFirstName() . ' ' . $order->getBillingAddress()->getLastName()),
                    'type' => 'individual',
                    'documents' => [
                        [
                            'type' => 'cpf',
                            'number' => preg_replace("/[^0-9]/", "", $pagarmebl_cpf)
                        ]
                    ],
                    'phone_numbers' => ['+' . preg_replace("/[^0-9]/", "", $order->getBillingAddress()->getData('telephone'))],
                    'email' => $order->getData('customer_email')
                ],
                'billing' => [
                    'name' => $this->_removerCaracter($order->getBillingAddress()->getFirstName() . ' ' . $order->getBillingAddress()->getLastName()),
                    'address' => [
                        'country' => strtolower($order->getBillingAddress()->getData('country_id')),
                        'street' => $this->_removerCaracter($rua_b),
                        'street_number' => $numero_b,
                        'state' => $this->buscarUf($order->getBillingAddress()->getRegionId()),
                        'city' => $this->_removerCaracter($order->getBillingAddress()->getCity()),
                        'neighborhood' => $this->_removerCaracter($bairro_b),
                        'zipcode' => preg_replace("/[^0-9]/", "", $order->getBillingAddress()->getPostcode())
                    ]
                ],
                'shipping' => [
                    'name' => $rua_s,
                    'fee' => 0,
                    'delivery_date' => date('Y-m-d'),
                    'expedited' => false,
                    'address' => [
                        'country' => strtolower($order->getShippingAddress()->getData('country_id')),
                        'street' => $this->_removerCaracter($rua_s),
                        'street_number' => $numero_s,
                        'state' => $this->buscarUf($order->getShippingAddress()->getRegionId()),
                        'city' => $this->_removerCaracter($order->getShippingAddress()->getCity()),
                        'neighborhood' => $this->_removerCaracter($bairro_s),
                        'zipcode' => preg_replace("/[^0-9]/", "", $order->getShippingAddress()->getPostcode())
                    ]
                ],
                'items' => $product_order

            ];
            $api_type_v2 = $this->getCoreConfig('payment/pagarmeconfig/api_type');
            if($api_type_v2){
                $array_input['customer']['document_number'] = $pagarmebl_cpf;
            }
            $transaction = $this->_cliente->transactions()->create($array_input);
            $paymentInfo->setAdditionalInformation('pagarme_transactions_id', $transaction->id);
            $paymentInfo->setAdditionalInformation('pagarme_acquirer_id', $transaction->acquirer_id);
            $paymentInfo->setAdditionalInformation('pagarme_authorization_code', $transaction->authorization_code);

            $paymentInfo->setAdditionalInformation('pagarme_boleto_url', $transaction->boleto_url);
            $paymentInfo->setAdditionalInformation('boleto_barcode', $transaction->boleto_barcode);

            $this->setLog(json_encode($paymentInfo));
        } catch (PagarMeException $e) {
            $this->setLog(json_encode($e->getMessage()));
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        }
    }

    public function _removerCaracter($string)
    {
        return preg_replace(array("/(ç|Ç)/", "/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/"), explode(" ", "c a A e E i I o O u U n N"), $string);
    }

    public function cancelPymentPagarMe($id_pagarme)
    {
        $this->_cliente->transactions()->refund([
            'id' => $id_pagarme,
        ]);
    }

    public function consultPymentPagarMe($id_pagarme)
    {
        $transactionPayables = $this->_cliente->transactions()->getList([
            'id' => $id_pagarme
        ]);
        return $transactionPayables[0]->status;
    }

    public function addPayCreditCard($paymentInfo, $amount)
    {
        $info = $paymentInfo->getData('additional_information');
        $order = $paymentInfo->getOrder();
        if (empty($info['cc_cid']) ||
            empty($info['pagarmecc_cpf']) ||
            empty($info['fullname']) ||
            empty($info['cc_exp_year']) ||
            empty($info['cc_number'])
        ) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Preecha todos os Campos do Cartão.'));
        }
        $cc_cid = $info['cc_cid'];
        $pagarmecc_cpf = (isset($info['pagarmecc_cpf']) ? $info['pagarmecc_cpf'] : $order->getBillingAddress()->getVatId());
        $cc_exp_year = $info['cc_exp_year'];

        if (strlen($cc_exp_year) == 4) {
            $cc_exp_year = substr($cc_exp_year, 2, strlen($cc_exp_year));
        }


        $cc_exp = str_pad($info['cc_exp_month'], 2, "0", STR_PAD_LEFT) . $cc_exp_year;
        $cc_number = $info['cc_number'];
        $fullname = $info['fullname'];
        $installments = $info['installments'];

        $street_b = $order->getBillingAddress()->getStreet();

        if (count($street_b) == 4) {
            $rua_b = (isset($street_b[0]) ? $street_b[0] : '..');
            $complemento_b = (isset($street_b[2]) ? $street_b[2] : '..');
            $numero_b = (isset($street_b[1]) ? $street_b[1] : '..');
            $bairro_b = (isset($street_b[3]) ? $street_b[3] : '..');
        } else {
            $rua_b = (isset($street_b[0]) ? $street_b[0] : '..');
            $complemento_b = '';
            $numero_b = (isset($street_b[1]) ? $street_b[1] : '..');
            $bairro_b = (isset($street_b[2]) ? $street_b[2] : '..');
        }

        $street_s = $order->getShippingAddress()->getStreet();

        if (count($street_s) == 4) {
            $rua_s = (isset($street_s[0]) ? $street_s[0] : '..');
            $complemento_s = (isset($street_s[2]) ? $street_s[2] : '..');
            $numero_s = (isset($street_s[1]) ? $street_s[1] : '..');
            $bairro_s = (isset($street_s[3]) ? $street_s[3] : '..');
        } else {
            $rua_s = (isset($street_s[0]) ? $street_s[0] : '..');
            $complemento_s = '';
            $numero_s = (isset($street_s[1]) ? $street_s[1] : '..');
            $bairro_s = (isset($street_s[2]) ? $street_s[2] : '..');
        }

        $product_order = [];
        foreach ($order->getItems() as $key => $item) {
            $product_order[$key]['id'] = $item->getProductId();
            $product_order[$key]['title'] = $this->_removerCaracter($item->getSku());
            $product_order[$key]['unit_price'] = str_replace(".", "", number_format((float)$item->getPrice(), 2, '.', ''));
            $product_order[$key]['quantity'] = $item->getQtyOrdered();
            $product_order[$key]['tangible'] = true;
        }

        try {
            $payment_action = $this->getCoreConfig('payment/pagarmecc/payment_action');
            $api_type_v2 = $this->getCoreConfig('payment/pagarmeconfig/api_type');

            if($api_type_v2){
                $phone_number_v2 = preg_replace("/[^0-9]/", "", $order->getBillingAddress()->getData('telephone'));
                $ddd = substr($phone_number_v2, 0, 2);
                $number = substr($phone_number_v2, 2, strlen($phone_number_v2));
                $array_type = [
                    "card_number" => $cc_number,
                    "card_cvv" => $cc_cid,
                    "card_holder_name" => $fullname,
                    "card_expiration_date" => $cc_exp,
                    "customer" => [
                        "email" => $order->getData('customer_email'),
                        "name" => $fullname,
                        "document_number" => preg_replace("/[^0-9]/", "", $pagarmecc_cpf),
                        "address" => [
                            "zipcode" => preg_replace("/[^0-9]/", "", ($order->getBillingAddress()->getPostcode() ? $order->getBillingAddress()->getPostcode() : $order->getShippingAddress()->getPostcode())),
                            "neighborhood" => $this->_removerCaracter(($bairro_b == '..' ? $bairro_s : $bairro_b )),
                            "street" => $this->_removerCaracter(($rua_b == '..' ? $rua_s : $rua_b )),
                            "street_number" => ($numero_b == '..' ? $numero_s : $numero_b ),
                        ],
                        "phone" => [
                            "number" => $number,
                            "ddd" => $ddd
                        ]
                    ],
                    "capture" => true,
                    "async" => false,
                    "installments" => $installments,
                    "payment_method" => "credit_card",
                    "amount" => str_replace(".", "", number_format((float)$amount, 2, '.', '')),
                    "postback_url" => $this->baseUrlLoja()
                ];
            }else {
                $array_type = [
                    'amount' => str_replace(".", "", number_format((float)$amount, 2, '.', '')),
                    "capture" => ($payment_action == 'authorize_capture' ? true : false),
                    'installments' => $installments,
                    'payment_method' => 'credit_card',
                    'card_holder_name' => $fullname,
                    'card_cvv' => $cc_cid,
                    'card_number' => $cc_number,
                    'card_expiration_date' => $cc_exp,
                    'customer' => [
                        'external_id' => preg_replace("/[^0-9]/", "", $pagarmecc_cpf),
                        'name' => $fullname,
                        'type' => 'individual',
                        'country' => strtolower($order->getBillingAddress()->getData('country_id')),
                        'documents' => [
                            [
                                'type' => 'cpf',
                                'number' => preg_replace("/[^0-9]/", "", $pagarmecc_cpf)
                            ]
                        ],
                        'phone_numbers' => ['+' . preg_replace("/[^0-9]/", "", $order->getBillingAddress()->getData('telephone'))],
                        'email' => $order->getData('customer_email')
                    ],
                    'billing' => [
                        'name' => $this->_removerCaracter($order->getBillingAddress()->getFirstName() . ' ' . $order->getBillingAddress()->getLastName()),
                        'address' => [
                            'country' => strtolower($order->getBillingAddress()->getData('country_id')),
                            'street' => $this->_removerCaracter($rua_b),
                            'street_number' => $numero_b,
                            'state' => $this->buscarUf($order->getBillingAddress()->getRegionId()),
                            'city' => $this->_removerCaracter($order->getBillingAddress()->getCity()),
                            'neighborhood' => $this->_removerCaracter($bairro_b),
                            'zipcode' => preg_replace("/[^0-9]/", "", $order->getBillingAddress()->getPostcode())
                        ]
                    ],
                    'shipping' => [
                        'name' => $rua_s,
                        'fee' => 0,
                        'delivery_date' => date('Y-m-d'),
                        'expedited' => false,
                        'address' => [
                            'country' => strtolower($order->getShippingAddress()->getData('country_id')),
                            'street' => $this->_removerCaracter($rua_s),
                            'street_number' => $numero_s,
                            'state' => $this->buscarUf($order->getShippingAddress()->getRegionId()),
                            'city' => $this->_removerCaracter($order->getShippingAddress()->getCity()),
                            'neighborhood' => $this->_removerCaracter($bairro_s),
                            'zipcode' => preg_replace("/[^0-9]/", "", $order->getShippingAddress()->getPostcode())
                        ]
                    ],
                    'items' => $product_order

                ];
            }
            
            $transaction = $this->_cliente->transactions()->create($array_type);

            if ($transaction->status == 'refused') {
                $paymentInfo->setAdditionalInformation('pagarme_transactions_refused', '1');
            }
            $paymentInfo->setAdditionalInformation('pagarme_transactions_id', $transaction->id);
            $paymentInfo->setAdditionalInformation('pagarme_acquirer_id', $transaction->acquirer_id);
            $paymentInfo->setAdditionalInformation('pagarme_fullname', $transaction->card_holder_name);
            $paymentInfo->setAdditionalInformation('pagarme_installments', $transaction->installments);
            $paymentInfo->setAdditionalInformation('pagarme_authorization_code', $transaction->authorization_code);

            $this->setLog(json_encode($paymentInfo));
        } catch (PagarMeException $e) {
            $this->setLog(json_encode($e->getMessage()));
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        }

        return $transaction;
    }

    public function baseUrlLoja(){
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
}