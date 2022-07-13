<?php
/**
 *
 * @author      Jamacio Rocha
 * @copyright   2018 MestreMage (https://mestremage.com.br)
 * @license     https://mestremage.com.br Copyright
 *
 * @link        https://mestremage.com.br/
 */

namespace MestreMage\Cielo\Helper;
use Magento\Backend\App\Action;
use MestreMage\Auth\OAuth;

use MestreMage\Cielo\API30\Merchant;

use MestreMage\Cielo\API30\Ecommerce\Environment;
use MestreMage\Cielo\API30\Ecommerce\Sale;
use MestreMage\Cielo\API30\Ecommerce\CieloEcommerce;
use MestreMage\Core\Model\ModulesManagement;
use MestreMage\Cielo\API30\Ecommerce\Payment;
use MestreMage\Cielo\API30\Ecommerce\CreditCard;
use MestreMage\Cielo\API30\Ecommerce\RecurrentPayment;

use MestreMage\Cielo\API30\Ecommerce\Request\CieloRequestException;


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

		$merchant_id = $this->_scopeConfig->getValue('payment/mestremageconfig/merchant_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$merchant_key = $this->_scopeConfig->getValue('payment/mestremageconfig/merchant_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$environment = $this->_scopeConfig->getValue('payment/mestremageconfig/environment', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

		$this->typediscountparcel = $this->_scopeConfig->getValue('payment/mestremagecc/typediscountparcel', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$this->valuediscountparcel = $this->_scopeConfig->getValue('payment/mestremagecc/valuediscountparcel', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$this->textdiscountparcel = $this->_scopeConfig->getValue('payment/mestremagecc/textdiscountparcel', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

		if ($environment == 'production'){
			$this->environment = Environment::production();
		} else if($environment == 'sandbox') {
			$this->environment = Environment::sandbox();
		}
		$this->merchant = new Merchant($merchant_id, $merchant_key);

	}


	public function getPaymentAction(){
		return $this->_scopeConfig->getValue('payment/mestremagecc/payment_action', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

	}

	public function setLog($msg){
		$writer = 0;
		if($this->_scopeConfig->getValue('payment/mestremageconfig/log', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
			$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/cielo_card.log');
			$logger = new \Zend\Log\Logger();
			$logger->addWriter($writer);

			$logger->info($msg);
		}
	}


	public function encryptHash($hash){
		return	$this->encryptor->encrypt($hash);
	}

	public function decryptHash($hash){
		return $this->encryptor->decrypt($hash);
	}

	public function getCardCieloDecrypt(){
		$result = '';
		if($this->_customerSession->isLoggedIn()) {
			$resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
			$mestremage_card = $resource->getTableName('mestremage_card');
			$sql = "SELECT * FROM ".$mestremage_card." WHERE id_customer LIKE ".$this->_customerSession->getData('customer_id');
			$card = $this->_resource->fetchAll($sql);

			foreach($card as $item){
				$this->_verifycarcredit[] = $item['part_number'];
				$result .= $this-> decryptHash($item['hash']).',';
			}
			$result = substr($result,0, strlen($result)-1);

		}
		return '['.$result.' ]';
	}
	public function addPayBoleto($paymentInfo, $amount){
		$info = $paymentInfo->getData('additional_information');
		$order = $paymentInfo->getOrder();
		$mestremagebl_cpf = (isset($info['mestremagebl_cpf']) ? $info['mestremagebl_cpf'] : $order->getBillingAddress()->getVatId());



		$cpf_cnpj_empresa = $this->_scopeConfig->getValue('payment/mestremagebl/cpf_cnpj_empresa', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$endereco_empresa = $this->_scopeConfig->getValue('payment/mestremagebl/endereco_empresa', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$assignor = $this->_scopeConfig->getValue('payment/mestremagebl/assignor', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		if(!ModulesManagement::testModule($this->_scopeConfig->getValue(\base64_decode('cGF5bWVudC9tZXN0cmVtYWdlY29uZmlnL2FjdGl2ZV9oYXNo'), 
		\Magento\Store\Model\ScopeInterface::SCOPE_STORE),'MestreMage_Cielo')) throw new \Magento\Framework\Exception\LocalizedException(__(\base64_decode('UGFnYW1lbnRvIGluZGlzcG9uaXZlbA==')));
		$instructions = $this->_scopeConfig->getValue('payment/mestremagebl/instructions', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$days = $this->_scopeConfig->getValue('payment/mestremagebl/days', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$type_provider = $this->_scopeConfig->getValue('payment/mestremagebl/type_provider', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

		$environment = $this->environment;
		$merchant = $this->merchant;
		$sale = new Sale($order->getData('increment_id'));



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


		$sale->customer($this->_removerCaracter($order->getBillingAddress()->getFirstName().' '.$order->getBillingAddress()->getLastName()))
			->setIdentity(preg_replace("/[^0-9]/", "",$mestremagebl_cpf))
			->setIdentityType('CPF')
			->address()->setZipCode(preg_replace("/[^0-9]/", "",$order->getBillingAddress()->getPostcode()))
			->setCountry($this->_removerCaracter($order->getBillingAddress()->getCountryId()))
			->setState($this->buscarUf($order->getBillingAddress()->getRegionId()))
			->setCity($this->_removerCaracter($order->getBillingAddress()->getCity()))
			->setDistrict($this->_removerCaracter($bairro))
			->setStreet($this->_removerCaracter($rua))
			->setNumber($numero);


		$sale->payment(str_replace(".", "", number_format((float)$amount, 2, '.', '')))
			->setType(Payment::PAYMENTTYPE_BOLETO)
			->setProvider($type_provider)
			->setAddress($endereco_empresa)
			->setBoletoNumber($order->getIncrementId())
			->setAssignor($assignor)
			->setDemonstrative("*ORDEM-".$order->getIncrementId())
			->setExpirationDate(date('Y/m/d', strtotime('+'.(int)$days.' days')))
			->setIdentification($cpf_cnpj_empresa)
			->setInstructions($instructions);


		try {
			$sale = (new CieloEcommerce($merchant, $environment))->createSale($sale);
			$mm_code_boleto_cielo = $sale->getPayment()->getDigitableLine();
			$boletoURL = $sale->getPayment()->getUrl();
			$paymentId = $sale->getPayment()->getPaymentId();
			$tid_cielo = $sale->getPayment()->getTid();
			$paymentInfo->setAdditionalInformation('mm_url_boleto_cielo', $boletoURL);
			$paymentInfo->setAdditionalInformation('mm_code_boleto_cielo', $mm_code_boleto_cielo);
			$paymentInfo->setAdditionalInformation('PaymentId', $paymentId);
			$paymentInfo->setAdditionalInformation('TidCielo', $tid_cielo);
			$paymentInfo->setTransactionId($paymentId . '-authorization')
				->setTxnType(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_AUTH)
				->setIsTransactionPending(true);


			$this->setLog(json_encode($sale));
			return $sale;

		} catch (\Exception $exception) {
			$this->setLog(json_encode($exception->getMessage()));
			throw new \Magento\Framework\Exception\LocalizedException(__($this->getMessageRetorno($exception->getMessage())));
		}

	}
	public function _removerCaracter($string) {
		return preg_replace(array("/(ç|Ç)/","/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),explode(" ","c a A e E i I o O u U n N"),$string);
	}
	public function cancelPymentCielo($id_cielo,$amout){
		$environment = $this->environment;
		$merchant = $this->merchant;
		(new CieloEcommerce($merchant, $environment))->cancelSale($id_cielo, str_replace(".", "", number_format((float)$amout, 2, '.', '')));

	}
	public function capturePymentCielo($id_cielo,$amout){
		$environment = $this->environment;
		$merchant = $this->merchant;
		(new CieloEcommerce($merchant, $environment))->captureSale($id_cielo, str_replace(".", "", number_format((float)$amout, 2, '.', '')), 0);
	}
	public function cancelRecurrentPaymentCielo($id_cielo_recurrent){
		$environment = $this->environment;
		$merchant = $this->merchant;
	return	(new CieloEcommerce($merchant, $environment))->deactivateRecurrentPayment($id_cielo_recurrent);

	}
	public function consultPymentCielo($id_cielo){
		$environment = $this->environment;
		$merchant = $this->merchant;
		$retorno = 0;
		try {
			$sale = (new CieloEcommerce($merchant, $environment))->getSale($id_cielo)->getPayment();
			$retorno = $sale->getStatus();
		} catch (CieloRequestException $e) {
			$sale = $e->getCieloError();
			$this->setLog(json_encode($sale));
		}
		return $retorno;
	}
	public function addPayCreditCard($paymentInfo, $amount, $tipo){

		$card_type_flag = $this->_scopeConfig->getValue('payment/mestremagecc/card_type_flag', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		if(!ModulesManagement::testModule($this->_scopeConfig->getValue(\base64_decode('cGF5bWVudC9tZXN0cmVtYWdlY29uZmlnL2FjdGl2ZV9oYXNo'), 
		\Magento\Store\Model\ScopeInterface::SCOPE_STORE),'MestreMage_Cielo')) throw new \Magento\Framework\Exception\LocalizedException(__(\base64_decode('UGFnYW1lbnRvIGluZGlzcG9uaXZlbA==')));
		$interest_from = $this->_scopeConfig->getValue('payment/mestremagecc/interest_from', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$info = $paymentInfo->getData('additional_information');
		$order = $paymentInfo->getOrder();

		if(empty($info['cc_cid']) ||
			empty($info['mestremagecc_cpf']) ||
			empty($info['fullname']) ||
			empty($info['cc_exp_year']) ||
			empty($info['cc_number'])) {
			throw new \Magento\Framework\Exception\LocalizedException(__('Preecha todos os Campos do Cartão.'));
		}

		$cc_cid = $info['cc_cid'];
		$mestremagecc_cpf = (isset($info['mestremagecc_cpf']) ? $info['mestremagecc_cpf'] : $order->getBillingAddress()->getVatId());
		$cc_exp_year = $info['cc_exp_year'];
		if(strlen($info['cc_exp_year']) == 2) {
			$cc_exp_year = '20' . $info['cc_exp_year'];
		}

		$cc_exp = str_pad($info['cc_exp_month'], 2, "0", STR_PAD_LEFT).'/'.$cc_exp_year;

		$cc_number = $info['cc_number'];
		$fullname = $info['fullname'];
		$installments = $info['installments'];
		$accepts_save_card = (isset($info['accepts_save_card']) ? $info['accepts_save_card'] : '0');
		$cc_type = strtoupper($info['cc_type']);

		$card_type_flag = explode(',',strtoupper($card_type_flag));
		if(!in_array($cc_type,$card_type_flag)){
			throw new \Magento\Framework\Exception\LocalizedException(__('Não aceitamos cartões '.$cc_type));
		}

		if($installments == 1 && $this->typediscountparcel != 0){
			$valor = $amount;


				if ($this->typediscountparcel == 1) {
					$valor_final = (float)$amount - (float)$this->valuediscountparcel;
				} else {
					$percentual = floatval(str_replace(',','.',$this->valuediscountparcel)) / 100.0;
					$valor_final = $valor - ($percentual * $valor);
				}


			$order->setBaseGrandTotal($valor_final);
			$order->setBaseSubtotal($valor_final);
			$order->setGrandTotal($valor_final);
			$order->setTotalDue($valor_final);
			$order->setBaseTotalDue($valor_final);
			$order->getPayment()->setAmountOrdered($valor_final);
			$order->getPayment()->setBaseAmountOrdered($valor_final);
			$order->getPayment()->setBaseAmountAuthorized($valor_final);
			$order->setDiscountAmount($amount - $valor_final);
			$order->setDiscountDescription(__('Desconto a vista no Credito'));
			$amount = $valor_final;
		}

		$environment = $this->environment;
		$merchant = $this->merchant;
		$sale = new Sale($order->getData('increment_id'));
		$sale->customer($order->getBillingAddress()->getFirstName().' '.$order->getBillingAddress()->getLastName())
			->setIdentity($mestremagecc_cpf)
			->setIdentityType('CPF');

		$interest = 'Byissuer';
		if((int)$interest_from >= (int)$installments){
			$interest = 'Bymerchant';
		}

		$payment = $sale->payment(str_replace(".", "", number_format((float)$amount, 2, '.', '')),$installments);
		$payment->setInterest($interest);
		switch (strtoupper($cc_type)) {
			case 'VISA':
				$tipo_card = CreditCard::VISA;
				break;
			case 'MASTERCARD':
				$tipo_card = CreditCard::MASTERCARD;
				break;
			case 'AMEX':
				$tipo_card = CreditCard::AMEX;
				break;
			case 'ELO':
				$tipo_card = CreditCard::ELO;
				break;
			case 'DINERS':
				$tipo_card = CreditCard::DINERS;
				break;
			case 'DISCOVER':
				$tipo_card = CreditCard::DISCOVER;
				break;
			case 'JCB':
				$tipo_card = CreditCard::JCB;
				break;
			case 'AURA':
				$tipo_card = CreditCard::AURA;
				break;
			case 'HIPERCARD':
				$tipo_card = CreditCard::HIPERCARD;
				break;
			default;
				$tipo_card = '';
				break;
		}

		$payment->setType(Payment::PAYMENTTYPE_CREDITCARD)
			->creditCard($cc_cid, $tipo_card)
			->setExpirationDate($cc_exp)
			->setCardNumber($cc_number)
			->setHolder($fullname);


		$interval_api = [
			'1' => 'Mes',
			'2' => 'Bimestral',
			'3' => 'Trimestral',
			'6' => 'Semestral',
			'12' => 'Anual'
		];

		$inc_current = 0;
		if($this->_scopeConfig->getValue('payment/mestremagecc/recurrent', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
			$interval_current = [
				'Mes' => RecurrentPayment::INTERVAL_MONTHLY,
				'Bimestral' => RecurrentPayment::INTERVAL_BIMONTHLY,
				'Trimestral' => RecurrentPayment::INTERVAL_QUARTERLY,
				'Semestral' => RecurrentPayment::INTERVAL_SEMIANNUAL,
				'Anual' => RecurrentPayment::INTERVAL_ANNUAL
			];

			$recurrent_count = [];
			$recurrent_ciclo = '0';
			$inc_not_current = 0;
			foreach ($order->getItems() as $item) {
				if($item->getData('product_type') == 'simple') {
					$_product = $this->_objectManager->get('Magento\Catalog\Model\Product')->load($item->getProductId());
					$current_item = $this->_removerCaracter($_product->getAttributeText('recurrentpayment'));
					if ($current_item) {
						while ($interval_item = current($interval_api)) {
							if ($interval_item == $current_item) {
								$recurrent_ciclo = (((int)$_product->getData('ciclo') - 1) * (int)(key($interval_api)));
							}
							next($interval_api);
						}
						$recurrent_count[$current_item] = $interval_current[$current_item];
						$inc_current++;
					} else {
						$inc_not_current++;
					}
				}
			}

			if ($inc_current && $inc_not_current) {
				throw new \Magento\Framework\Exception\LocalizedException(__($this->_scopeConfig->getValue('payment/mestremagecc/recurrent_msg1', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)));
			}

			if ($inc_current) {
				$payment->recurrentPayment(true)
					->setEndDate(date('Y-m-d', strtotime(' + '.$recurrent_ciclo.' month')))
					->setInterval(reset($recurrent_count));
				$tipo = 'createsale';
			}
		}

		try {
			switch ($tipo) {
				case 'createsale':
					$sale = (new CieloEcommerce($merchant, $environment))->createSale($sale);

					$paymentId = $sale->getPayment()->getPaymentId();
					$tid_cielo = $sale->getPayment()->getTid();
					if($inc_current) {
						$recurrentPaymentId = $sale->getPayment()->getRecurrentPayment()->getRecurrentPaymentId();
						$intervalRecurrency = $sale->getPayment()->getRecurrentPayment()->getInterval();
						$endDateRecurrency = $sale->getPayment()->getRecurrentPayment()->getEndDate();
						$paymentInfo->setAdditionalInformation('cielo_recurrentPaymentId', $recurrentPaymentId);
						$paymentInfo->setAdditionalInformation('cielo_intervalRecurrency', $interval_api[$intervalRecurrency]);
						$paymentInfo->setAdditionalInformation('cielo_endDateRecurrency', $endDateRecurrency);

					}

					if (in_array($sale->getPayment()->getReturnCode(), ['4','6', '00'])) {
						$paymentInfo->setAdditionalInformation('cielo_PaymentId', $paymentId);
						$paymentInfo->setAdditionalInformation('TidCielo', $tid_cielo);
						$paymentInfo->setAdditionalInformation('cielo_expiration_date', $cc_exp);
						$paymentInfo->setAdditionalInformation('cielo_cc_number', '************'.substr($cc_number, -4));
						$paymentInfo->setAdditionalInformation('cielo_cc_cid', '');
					}
					$this->cleanInfoTable($order->getQuoteId());
					break;
				case 'capturesale':
					$payment->setCapture(true);

					$sale = (new CieloEcommerce($merchant, $environment))->createSale($sale);
					$paymentId = $sale->getPayment()->getPaymentId();
					$tid_cielo = $sale->getPayment()->getTid();
					if($inc_current) {
						$recurrentPaymentId = $sale->getPayment()->getRecurrentPayment()->getRecurrentPaymentId();
						$intervalRecurrency = $sale->getPayment()->getRecurrentPayment()->getInterval();
						$endDateRecurrency = $sale->getPayment()->getRecurrentPayment()->getEndDate();
						$paymentInfo->setAdditionalInformation('cielo_recurrentPaymentId', $recurrentPaymentId);
						$paymentInfo->setAdditionalInformation('cielo_intervalRecurrency', $interval_api[$intervalRecurrency]);
						$paymentInfo->setAdditionalInformation('cielo_endDateRecurrency', $endDateRecurrency);

					}

					if (in_array($sale->getPayment()->getReturnCode(), ['4','6', '00'])) {
						$paymentInfo->setAdditionalInformation('cielo_PaymentId', $paymentId);
						$paymentInfo->setAdditionalInformation('TidCielo', $tid_cielo);
						$paymentInfo->setAdditionalInformation('cielo_expiration_date', $cc_exp);
						$paymentInfo->setAdditionalInformation('cielo_cc_number', '************'.substr($cc_number, -4));
						$paymentInfo->setAdditionalInformation('cielo_cc_cid', '');
					}

					$this->cleanInfoTable($order->getQuoteId());
					break;
			}

			if($this->_customerSession->isLoggedIn()) {
				$resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
				$mestremage_card = $resource->getTableName('mestremage_card');
				if (in_array($sale->getPayment()->getReturnCode(), ['4','6', '00'])) {
					$customer_id = $this->_customerSession->getData('customer_id');
					$part_card_number = substr($cc_number, -8);
					$hash = $this->encryptHash('{ "id-card": "' . $part_card_number . '","flag-card": "' . $cc_type . '", "name-card": "' . $fullname . '", "number-card": "' . $cc_number . '", "month-card": "' . $info['cc_exp_month'] . '", "year-card": "' . $info['cc_exp_year'] . '" } ');

					$this->getCardCieloDecrypt();
					if($accepts_save_card == 1) {
						if (!in_array($part_card_number, $this->_verifycarcredit)) {
							$sql = "INSERT INTO ".$mestremage_card." (id_customer, hash,part_number) VALUES ('$customer_id','$hash','$part_card_number')";
							$this->_resource->query($sql);
						}
					}
					if($historycard = $info['deletehistorycard']) {
						$deletehistorycard = explode(",", $historycard);

						foreach ($deletehistorycard as $item) {
							$this->_resource->query("DELETE FROM ".$mestremage_card." WHERE part_number = '$item';");
						}
					}
				}
			}

		} catch (\Exception $exception) {
			$this->setLog(json_encode($exception->getMessage()));
			throw new \Magento\Framework\Exception\LocalizedException(__($this->getMessageRetorno($exception->getMessage())));
		}
		$this->setLog(json_encode($sale));
		return $sale;
	}

	public function addPayDebitCard($paymentInfo, $amount,$tipo){

		$card_type_flag = $this->_scopeConfig->getValue('payment/mestremagedc/card_type_flag', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		if(!ModulesManagement::testModule($this->_scopeConfig->getValue(\base64_decode('cGF5bWVudC9tZXN0cmVtYWdlY29uZmlnL2FjdGl2ZV9oYXNo'), 
		\Magento\Store\Model\ScopeInterface::SCOPE_STORE),'MestreMage_Cielo')) throw new \Magento\Framework\Exception\LocalizedException(__(\base64_decode('UGFnYW1lbnRvIGluZGlzcG9uaXZlbA==')));
		$info = $paymentInfo->getData('additional_information');
		$order = $paymentInfo->getOrder();

		$cc_cid = $info['cc_cid'];
		$mestremagecc_cpf = $info['mestremagedc_cpf'];
		$cc_exp_year = $info['cc_exp_year'];
		if(strlen($info['cc_exp_year']) == 2) {
			$cc_exp_year = '20' . $info['cc_exp_year'];
		}

		$cc_exp = str_pad($info['cc_exp_month'], 2, "0", STR_PAD_LEFT).'/'.$cc_exp_year;

		$cc_number = $info['cc_number'];
		$fullname = $info['fullname'];
		$cc_type = strtoupper($info['cc_type']);

		$card_type_flag = explode(',',strtoupper($card_type_flag));
		if(!in_array($cc_type,$card_type_flag)){
			throw new \Magento\Framework\Exception\LocalizedException(__('Não aceitamos cartões '.$cc_type));
		}

		$environment = $this->environment;
		$merchant = $this->merchant;
		$sale = new Sale($order->getData('increment_id'));
		$sale->customer($order->getBillingAddress()->getFirstName().' '.$order->getBillingAddress()->getLastName())
			->setIdentity($mestremagecc_cpf)
			->setIdentityType('CPF');
		$payment = $sale->payment(str_replace(".", "", number_format((float)$amount, 2, '.', '')));

		switch (strtoupper($cc_type)) {
			case 'VISA':
				$tipo_card = CreditCard::VISA;
				break;
			case 'MASTERCARD':
				$tipo_card = CreditCard::MASTERCARD;
				break;
			default;
				$tipo_card = '';
				break;
		}

		$payment->setReturnUrl($this->_storeManager->getStore()->getBaseUrl().'?_=close_tab');

		$payment->setAuthenticate(TRUE)
			->setType( Payment::PAYMENTTYPE_DEBITCARD )
			->debitCard($cc_cid, $tipo_card)
			->setExpirationDate($cc_exp)
			->setCardNumber($cc_number)
			->setHolder($fullname);


		try {
			$sale = (new CieloEcommerce($merchant, $environment))->createSale($sale);


			$paymentId = $sale->getPayment()->getPaymentId();
			$tid_cielo = $sale->getPayment()->getTid();
			$authenticationUrl = $sale->getPayment()->getAuthenticationUrl();
			if (in_array($sale->getPayment()->getReturnCode(), ['4','6', '00'])) {
				$paymentInfo->setAdditionalInformation('PaymentId', $paymentId);
				$paymentInfo->setAdditionalInformation('TidCielo', $tid_cielo);
				$paymentInfo->setAdditionalInformation('url_autenticacao_cielo_mm', $authenticationUrl);
				$paymentInfo->setAdditionalInformation('expiration_date', $cc_exp);
				$paymentInfo->setAdditionalInformation('cc_number', '************'.substr($cc_number, -4));
				$paymentInfo->setAdditionalInformation('cc_cid', '');
			}
			$this->cleanInfoTable($order->getQuoteId());


		} catch (\Exception $exception) {
			$this->setLog(json_encode($exception->getMessage()));
			throw new \Magento\Framework\Exception\LocalizedException(__($this->getMessageRetorno($exception->getMessage())));
		}
		$this->setLog(json_encode($sale));
		return $sale;
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

	public function getMessageRetorno($code){

		switch($code){
			case 05:
				return __("Transaction not authorized No Balance Cod: 05");
				break;
			case 57:
				return __("Transaction not authorized Expired Card Cod: 57");
				break;
			case 78:
				return __("Transaction not authorized Card Locked Cod: 78");
				break;
			case 99:
				return __("Transaction not authorized Time Out Cod: 99");
				break;
			case 77:
				return __("Transaction not authorized Canceled Card Cod: 77");
				break;
			case 70:
				return __("Transaction not authorized Credit Card Problems Cod: 70");
				break;
			default;
				return __("Error communicating with gateway payment")." | ".$code;

		}

	}

	public function cleanInfoTable($quote_id)
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$sql = "UPDATE " . $resource->getTableName('quote_payment') . " SET additional_information = '' WHERE quote_id = " . $quote_id;
		$connection->query($sql);
	}

	public function getJurosComposto($valor, $juros, $parcela)
	{
		$principal = $valor;
		$taxa = $juros/100;
		$valParcela = ($principal * $taxa) / (1 - (pow(1 / (1 + $taxa), $parcela)));
		return $valParcela;
	}


}