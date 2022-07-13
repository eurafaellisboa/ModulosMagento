<?php
namespace MestreMage\Cielo\Model;

use Magento\Framework\UrlInterface;
use \Magento\Payment\Model\Method\AbstractMethod;
use Magento\Sales\Model\Order;
use \Magento\Framework\Exception\LocalizedException;
use \Magento\Sales\Model\Order\Payment;



class PaymentMethodCc extends \Magento\Payment\Model\Method\Cc
{
    const ROUND_UP = 100;
    protected $_canAuthorize = true;
    protected $_canCapture = false;
    protected $_canRefund = true;
    protected $_code = 'mestremagecc';
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
    protected $_mestremageHelper;

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
        \MestreMage\Cielo\Helper\Data $mestremageHelper,
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
        $this->_mestremageHelper = $mestremageHelper;

        $paymentAction = $this->_mestremageHelper->getPaymentAction();
        if($paymentAction == 'authorize_capture'){
            $this->_canCapture = true;
        }

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

        try {
            $sale =  $this->_mestremageHelper->addPayCreditCard($payment, $amount,'createsale');

                if (!in_array($sale->getPayment()->getReturnCode(), ['4', '6', '00'])) {
     
                    if($this->getCoreConfig2('payment/mestremageconfig/valid_error_in_checkout')){
                        throw new \Magento\Framework\Exception\LocalizedException(__($this->_mestremageHelper->getMessageRetorno($sale->getPayment()->getReturnCode())));
                    }else{              
                        $payment->setAdditionalInformation('cielo_transactions_refused', (string)$this->_mestremageHelper->getMessageRetorno($sale->getPayment()->getReturnCode()));

                        $payment->setTransactionId((string)$this->_mestremageHelper->getMessageRetorno($sale->getPayment()->getReturnCode()))
                        ->setTxnType(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_VOID)
                        ->setIsTransactionClosed(true);

                    }
                }else{
                    $payment->setTransactionId($payment->getAdditionalInformation('cielo_PaymentId') . '-authorization')
                    ->setTxnType(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_AUTH)
                    ->setIsTransactionClosed(true);
                }


        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\LocalizedException(__($exception->getMessage()));
        }

        return $this;


    }

    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        try {
            $sale = $this->_mestremageHelper->addPayCreditCard($payment, $amount, 'capturesale');
                if (!in_array($sale->getPayment()->getReturnCode(), ['4','6', '00'])) {

                    if($this->getCoreConfig2('payment/mestremageconfig/valid_error_in_checkout')){
                        throw new \Magento\Framework\Exception\LocalizedException(__($this->_mestremageHelper->getMessageRetorno($sale->getPayment()->getReturnCode())));
                    }else{              
                        $payment->setAdditionalInformation('cielo_transactions_refused', (string)$this->_mestremageHelper->getMessageRetorno($sale->getPayment()->getReturnCode()));

                        $payment->setTransactionId((string)$this->_mestremageHelper->getMessageRetorno($sale->getPayment()->getReturnCode()))
                        ->setTxnType(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_VOID)
                        ->setIsTransactionClosed(true);

                    }

                }else{
                    $payment->setTransactionId($payment->getAdditionalInformation('cielo_PaymentId') . '-capture')
                    ->setTxnType(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE)
                    ->setIsTransactionClosed(true);
                }

        } catch (\Exception $exception) {
            $this->_mestremageHelper->setLog($exception->getMessage());
            throw new \Magento\Framework\Exception\LocalizedException(__($exception->getMessage()));
        }

        return $this;
    }

    public function getCoreConfig2($value){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $scopeConfig = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        return $scopeConfig->getValue($value, $storeScope);
    }

    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        if (!$this->isActive($quote ? $quote->getStoreId() : null)) {
            return false;
        }
        return true;
    }

}