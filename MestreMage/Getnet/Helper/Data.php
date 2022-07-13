<?php
/**
 *
 * @author      Jamacio Rocha
 * @copyright   2018 MestreMage (https://mestremage.com.br)
 * @license     https://mestremage.com.br Copyright
 *
 * @link        https://mestremage.com.br/
 */

namespace MestreMage\Getnet\Helper;
use Magento\Backend\App\Action;
use MestreMage\MestreMage;
use MestreMage\Auth\OAuth;



use MestreMage\Getnet\API\Transaction;
use MestreMage\Getnet\API\Getnet;
use MestreMage\Getnet\API\Token;




class Data extends \Magento\Framework\App\Helper\AbstractHelper {


	protected $_scopeConfig;
	protected $tokenauth;
	protected $keyauth;
	protected $_objectManager;
	protected $date;

	protected $getnet;
	protected $seller_id;
	protected $environment;


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

		$client_id = $this->_scopeConfig->getValue('payment/mestremagegn/client_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$client_secret = $this->_scopeConfig->getValue('payment/mestremagegn/client_secret', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$this->seller_id = $this->_scopeConfig->getValue('payment/mestremagegn/seller_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$this->environment = $this->_scopeConfig->getValue('payment/mestremagegn/environment', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);


		if($client_id && $client_secret)
			$this->getnet = new Getnet($client_id, $client_secret, $this->environment);


	}


	public function getPaymentAction(){
		return $this->_scopeConfig->getValue('payment/mestremagegn/payment_action', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

	}

	public function setLog($msg){
		$writer = 0;
		if($this->_scopeConfig->getValue('payment/mestremagegn/log', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
			$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/getnet_card.log');
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

	public function getCardGetnetDecrypt(){
		$result = '';
		if($this->_customerSession->isLoggedIn()) {
			$sql = "SELECT * FROM mestremage_card_getnet WHERE id_customer LIKE ".$this->_customerSession->getData('customer_id');
			$card = $this->_resource->fetchAll($sql);

			foreach($card as $item){
				$this->_verifycarcredit[] = $item['part_number'];
				$result .= $this-> decryptHash($item['hash']).',';
			}
			$result = substr($result,0, strlen($result)-1);

		}
		return '['.$result.' ]';
	}

	public function addPayCreditCard($paymentInfo, $amount,$tipo){


		$info = $paymentInfo->getData('additional_information');
		$order = $paymentInfo->getOrder();

		$cc_cid = $info['cc_cid'];
		$cc_number = preg_replace("/[^0-9]/", "", $info['cc_number']);
		$fullname = $info['fullname'];
		$installments = $info['installments'];
		$accepts_save_card = (isset($info['accepts_save_card']) ? $info['accepts_save_card'] : '0');
		$cc_type = strtoupper($info['cc_type']);

		$cc_exp_year = $info['cc_exp_year'];
		if(strlen ($cc_exp_year) == 4)
			$cc_exp_year = substr($cc_exp_year, -2);


		if(empty($cc_cid) || empty($cc_number) || empty($fullname) || empty($cc_type) || empty($cc_exp_year)){

			throw new \Magento\Framework\Exception\LocalizedException(__('Fill in the fields on the card'));
		}


		$street = $order->getBillingAddress()->getStreet();
		$TransactionType = "FULL";
		if($installments > 1)
			$TransactionType ="INSTALL_NO_INTEREST";

		if(count($street) == 4){
			$rua = (isset($street[0]) ? $street[0] : '');
			$complemento = (isset($street[2]) ? $street[2] : '');
			$numero = (isset($street[1]) ? $street[1] : '');
			$bairro = (isset($street[3]) ? $street[3] : '--');
		}else{
			$rua = (isset($street[0]) ? $street[0] : '');
			$complemento = '';
			$numero = (isset($street[1]) ? $street[1] : '');
			$bairro = (isset($street[2]) ? $street[2] : '--');
		}

		$cart = $this->_objectManager->get('\Magento\Checkout\Model\Cart')->getQuote();
		$session_id = $cart->getData('entity_id').preg_replace("/[^0-9]/", "", $cart->getData('created_at'));

		$transaction = new Transaction();
		$transaction->setSellerId($this->seller_id);
		$transaction->setCurrency("BRL");
		$transaction->setAmount(str_replace(".", "", number_format((float)$amount, 2, '.', '')));
		$card = new Token($cc_number, "customer_".$this->_customerSession->getData('customer_id'), $this->getnet);
		$transaction->Credit("")
			->setAuthenticated(false)
			->setDynamicMcc("1799")
			->setSoftDescriptor($this->environment."*ORDEM-".$order->getIncrementId())
			->setDelayed(false)
			->setPreAuthorization(false)
			->setNumberInstallments($installments)
			->setSaveCardData(false)
			->setTransactionType($TransactionType)
			->Card($card)
			->setBrand($cc_type)
			->setExpirationMonth(str_pad($info['cc_exp_month'], 2, "0", STR_PAD_LEFT))
			->setExpirationYear($cc_exp_year)
			->setCardholderName($fullname)
			->setSecurityCode($cc_cid);
		$transaction->Customer("customer_".($this->_customerSession->getData('customer_id') ? $order->getCustomerTaxvat() : 'NOT_LOGIN_'.time()) )
			->setDocumentType("CPF")
			->setEmail($order->getBillingAddress()->getEmail())
			->setFirstName($order->getBillingAddress()->getFirstName())
			->setLastName($order->getBillingAddress()->getLastName())
			->setName($fullname)
			->setPhoneNumber(preg_replace("/[^0-9]/", "", $order->getBillingAddress()->getTelephone()))
			->setDocumentNumber(($order->getCustomerTaxvat() ? $order->getCustomerTaxvat() : '00000000000'))
			->BillingAddress($order->getBillingAddress()->getPostcode())
			->setCity($order->getBillingAddress()->getCity())
			->setComplement($complemento)
			->setCountry($order->getBillingAddress()->getCountryId())
			->setDistrict($bairro)
			->setNumber($numero)
			->setPostalCode(substr(preg_replace("/[^0-9]/", "", $order->getBillingAddress()->getPostcode()), 0, 8))
			->setState($order->getBillingAddress()->getRegion())
			->setStreet($rua);

		$transaction->Shippings("")
			->setEmail($order->getBillingAddress()->getEmail())
			->setFirstName($order->getBillingAddress()->getFirstName())
			->setName($order->getBillingAddress()->getFirstName()." ".$order->getBillingAddress()->getLastName())
			->setPhoneNumber(preg_replace("/[^0-9]/", "", $order->getBillingAddress()->getTelephone()))
			->ShippingAddress($order->getBillingAddress()->getPostcode())
			->setCity($order->getBillingAddress()->getCity())
			->setComplement($complemento)
			->setCountry($order->getBillingAddress()->getCountryId())
			->setDistrict($bairro)
			->setNumber($numero)
			->setPostalCode(substr(preg_replace("/[^0-9]/", "", $order->getBillingAddress()->getPostcode()), 0, 8))
			->setState($order->getBillingAddress()->getRegion())
			->setStreet($rua);
		$transaction->Order($order->getIncrementId())
			->setProductType("service")
			->setSalesTax("0");
		$transaction->Device($session_id)
			->setIpAddress(getenv("REMOTE_ADDR"));

		$this->setLog(json_encode($transaction));

		$sale = '';

		try {

			switch ($tipo) {
				case 'createsale':
					$sale = $this->getnet->Authorize($transaction);
					break;
				case 'capturesale':
					$sale = $this->getnet->Authorize($transaction);
					$this->getnet->AuthorizeConfirm($sale->payment_id);
					break;
			}

			$this->setLog(json_encode($sale));

			$paymentInfo->setAdditionalInformation('payment_id_getnet', $sale->payment_id);
			$paymentInfo->setAdditionalInformation('status_getnet', $sale->status);
			$paymentInfo->setAdditionalInformation('installments_getnet', $installments);

			if($this->_customerSession->isLoggedIn()) {
				if ($sale->getStatus() == 'APPROVED') {
					$customer_id = $this->_customerSession->getData('customer_id');
					$part_card_number = substr($cc_number, -8);
					$hash = $this->encryptHash('{ "id-card": "' . $part_card_number . '","flag-card": "' . $cc_type . '", "name-card": "' . $fullname . '", "number-card": "' . $cc_number . '", "month-card": "' . $info['cc_exp_month'] . '", "year-card": "' . $info['cc_exp_year'] . '" } ');

					$this->getCardGetnetDecrypt();
					if($accepts_save_card == 1) {
						if (!in_array($part_card_number, $this->_verifycarcredit)) {
							$sql = "INSERT INTO mestremage_card_getnet (id_customer, hash,part_number) VALUES ('$customer_id','$hash','$part_card_number')";
							$this->_resource->query($sql);
						}
					}
					if($historycard = $info['deletehistorycard']) {
						$deletehistorycard = explode(",", $historycard);

						foreach ($deletehistorycard as $item) {
							$this->_resource->query("DELETE FROM mestremage_card_getnet WHERE part_number = '$item';");
						}
					}

				}
			}

		} catch (Exception $e) {
			$this->setLog($e);
		}

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

	public function getJurosComposto($valor, $juros, $parcela)
	{
		$principal = $valor;
		$taxa = $juros/100;
		$valParcela = ($principal * $taxa) / (1 - (pow(1 / (1 + $taxa), $parcela)));
		return $valParcela;
	}


}