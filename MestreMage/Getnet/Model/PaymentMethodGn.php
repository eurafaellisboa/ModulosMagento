<?php
namespace MestreMage\Getnet\Model;

use Magento\Framework\UrlInterface;
use \Magento\Payment\Model\Method\AbstractMethod;
use Magento\Sales\Model\Order;
use \Magento\Framework\Exception\LocalizedException;
use \Magento\Sales\Model\Order\Payment;



class PaymentMethodGn extends \Magento\Payment\Model\Method\Cc
{
    const ROUND_UP = 100;
    protected $_canAuthorize = true;
    protected $_canCapture = false;
    protected $_canRefund = true;
    protected $_code = 'mestremagegn';
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
        \MestreMage\Getnet\Helper\Data $mestremageHelper,
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
        $sale = $this->_mestremageHelper->addPayCreditCard($payment, $amount,'createsale');


        try {

            if ($sale->getStatus() != 'APPROVED') {
                $payment->setAdditionalInformation('PaymentId', $sale->payment_id);
                $msgRetorno = $sale->description;
                $this->_mestremageHelper->setLog(__("$msgRetorno").' | '.json_encode($sale));

                throw new \Magento\Framework\Exception\LocalizedException(__("$msgRetorno"));
            }

            $payment->setTransactionId($sale->payment_id . '-authorization')
                ->setTxnType(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_AUTH)
                ->setIsTransactionClosed(true);


        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\LocalizedException(__($exception->getMessage()));
        }


    }

    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        try {

            $captureSale = $this->_mestremageHelper->addPayCreditCard($payment, $amount, 'capturesale');

            $payment->setAdditionalInformation('GetnetStatus', $captureSale->getStatus());
            if ($captureSale->getStatus() != 'APPROVED') {

                $msgRetorno =  $captureSale->description;
                $this->_mestremageHelper->setLog(__("$msgRetorno") . ' | ' . json_encode($captureSale));
                throw new \Magento\Framework\Exception\LocalizedException(__("$msgRetorno"));
            }


            $payment->setTransactionId($captureSale->payment_id . '-authorization')
                ->setTxnType(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE)
                ->setIsTransactionClosed(true);

        } catch (\Exception $exception) {
            $this->_mestremageHelper->setLog($exception->getMessage());
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

}