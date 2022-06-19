<?php
namespace MestreMage\PagarMe\Model\Payment;

use Magento\Framework\UrlInterface;
use Magento\Sales\Model\Order;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order\Payment;
use MestreMage\PagarMe\Model\PagarMe;



class CreditCard extends \Magento\Payment\Model\Method\Cc
{
    const ROUND_UP = 100;
    protected $_canAuthorize = true;
    protected $_canCapture = true;
    protected $_code = 'pagarmecc';
    protected $_isGateway               = true;
    protected $_canCapturePartial       = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canVoid                = true;
    protected $_canCancel              = true;
    protected $_canUseForMultishipping = false;
    protected $_countryFactory;
    protected $_supportedCurrencyCodes = ['BRL'];
    protected $_debugReplacePrivateDataKeys = ['number', 'exp_month', 'exp_year', 'cvc'];
    protected $_cart;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Checkout\Model\Cart $cart,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $moduleList,
            $localeDate,
            null,
            null,
            $data
        );
        $this->_countryFactory = $countryFactory;
        $this->scopeConfig = $scopeConfig;
        $this->_cart = $cart;
    }

    public function assignData(\Magento\Framework\DataObject $data)
    {
        parent::assignData($data);
        $infoInstance = $this->getInfoInstance();
        $currentData = $data->getAdditionalData();
        foreach($currentData as $key=>$value){
            if($key == 'extension_attributes'){
                $value = ['mm'];
            }
            $infoInstance->setAdditionalInformation($key,$value);
        }
        return $this;
    }

    public function validate()
    {
        return $this;
    }

    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        return $this->getTransaction($payment, $amount);
    }

    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        return $this->getTransaction($payment, $amount);
    }

    public function getTransaction($payment, $amount){
        $pagar_me = new PagarMe();
        try {
            $sale = $pagar_me->addPayCreditCard($payment, $amount);
            if ($sale->status == 'refused') {

                if($this->getCoreConfig('payment/pagarmecc/processing_type')){
                    throw new \Magento\Framework\Exception\LocalizedException(__('Seu cartão não pode ser processado, entre em contato conosco.'));
                }else{
                    $payment->setTransactionId($sale->acquirer_id . '-refused')
                        ->setTxnType(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_VOID)
                        ->setIsTransactionClosed(true);
                }

            }else{
                $payment->setTransactionId($sale->acquirer_id . '-authorization')
                    ->setTxnType(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_AUTH)
                    ->setIsTransactionClosed(true);
            }
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\LocalizedException(__($exception->getMessage()));
        }
        return $this;
    }

    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        if (!$this->isActive($quote ? $quote->getStoreId() : null)) {
            return false;
        }
        return true;
    }

    public function getCoreConfig($valor)
    {
        $scopeConfig = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\Config\ScopeConfigInterface');
        return $scopeConfig->getValue($valor, \Magento\Store\Model\ScopeInterface::SCOPE_STORES);
    }

}