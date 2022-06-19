<?php
/**
 *
 * @author      Jamacio Rocha
 * @copyright   2018 MestreMage (https://mestremage.com.br)
 * @license     https://mestremage.com.br Copyright
 *
 * @link        https://mestremage.com.br/
 */
namespace MestreMage\PagarMe\Model\Source;

use Magento\Framework\Locale\Bundle\DataBundle;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Escaper;
use Magento\Payment\Model\CcConfig;
use Magento\Framework\View\Asset\Source;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Customer\Model\Session;
use MestreMage\PagarMe\Api\Client;


class ConfigProvider implements ConfigProviderInterface
{
    /**
     * Years range
     */
    const YEARS_RANGE = 20;
    /**
     * @var string[]
     */
    protected $methodCodes = [
        'pagarmecc'
    ];

    protected $_ccoptions = [
        'mastercard' => 'Mastercard',
        'visa' => 'Visa',
        'amex' => 'American Express',
        'diners' => 'Diners',
        'elo' => 'Elo',
        'hipercard' => 'Hipercard',
        'hiper' => 'HIPER',
        'discover' => 'Discover',
        'jcb' => 'JCB',
        'aura' => 'Aura'
    ];
    /**
     * @var \Magento\Payment\Model\Method\AbstractMethod[]
     */
    protected $methods = [];

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @param PaymentHelper $paymentHelper
     * @param Escaper $escaper
     */
    /**
     * @var array
     */
    private $icons = [];

    /**
     * @var CcConfig
     */
    protected $ccConfig;
    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $localeResolver;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_helper;
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $_priceCurrency;
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var \Magento\Framework\View\Asset\Source
     */
    protected $assetSource;
    protected $_priceFiler;

    /**
     * ConfigProvider constructor.
     * @param PaymentHelper $paymentHelper
     * @param Escaper $escaper
     * @param CcConfig $ccConfig
     * @param Source $assetSource
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param Session $customerSession
     * @param \Magento\Checkout\Model\Session $_checkoutSession
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        PaymentHelper $paymentHelper,
        Escaper $escaper,
        CcConfig $ccConfig,
        Source $assetSource,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        Session $customerSession,
        \Magento\Checkout\Model\Session $_checkoutSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Pricing\Helper\Data $priceFilter
    ) {
        $this->ccConfig = $ccConfig;
        $this->assetSource = $assetSource;
        $this->escaper = $escaper;
        $this->localeResolver = $localeResolver;
        $this->_date = $date;
        $this->_priceCurrency = $priceCurrency;
        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $_checkoutSession;
        $this->scopeConfig = $scopeConfig;
        $this->_priceFiler = $priceFilter;
        foreach ($this->methodCodes as $code) {
            $this->methods[$code] = $paymentHelper->getMethodInstance($code);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $config = [];
        foreach ($this->methodCodes as $code) {
            if ($this->methods[$code]->isAvailable()) {
                $config['payment'][$code]['ccavailabletypes'] = $this->getCcAvailableTypes();
                $config['payment'][$code]['years'] = $this->getYears();
                $config['payment'][$code]['months'] = $this->getMonths();
                $config['payment'][$code]['icons'] = $this->getIcons();
                $config['payment'][$code]['currency'] = $this->getCurrencyData();
                $config['payment'][$code]['type_interest'] = $this->TypeInstallment();
                $config['payment'][$code]['info_interest'] = $this->getInfoParcelamentoJuros();
                $config['payment'][$code]['max_installment'] = $this->MaxInstallment();
                $config['payment'][$code]['min_installment'] = $this->MinInstallment();
                $config['payment'][$code]['image_cvv'] = $this->getCvvImg();
                $config['payment'][$code]['type_view_credit_card'] = $this->layoutCardView();
            }
        }

        return $config;
    }

    /**
     * @return array
     */
    protected function getCcAvailableTypes()
    {
        return $this->_ccoptions;
    }

    public function getCvvImg(){
        $asset = $this->ccConfig
            ->createAsset('MestreMage_PagarMe::images/cc/cvv.gif');
        return $asset->getUrl();
    }
    /**
     * @return array
     */
    public function getIcons()
    {
        if (!empty($this->icons)) {
            return $this->icons;
        }

        $types = $this->_ccoptions;
        foreach (array_keys($types) as $code) {

            if (!array_key_exists($code, $this->icons)) {
                if (in_array($code, explode(',',$this->getFlagCard()))) {
                    $asset = $this->ccConfig
                        ->createAsset('MestreMage_PagarMe::images/cc/' . strtolower($code) . '.png');
                    $placeholder = $this->assetSource->findSource($asset);
                    if ($placeholder) {
                        list($width, $height) = getimagesize($asset->getSourceFile());
                        $this->icons[$code] = [
                            'url' => $asset->getUrl(),
                            'width' => $width,
                            'height' => $height
                        ];
                    }
                }
            }
        }
        return $this->icons;
    }

    /**
     * @return array
     */
    public function getMonths()
    {
        $data = [];
        $months = (new DataBundle())->get(
            $this->localeResolver->getLocale()
        )['calendar']['gregorian']['monthNames']['format']['wide'];
        foreach ($months as $key => $value) {
            $monthNum = ++$key < 10 ? '0' . $key : $key;
            $data[$key] = $monthNum . ' - ' . $value;
        }
        return $data;
    }

    /**
     * @return array
     */
    public function getYears()
    {
        $years = [];
        $first = (int)$this->_date->date('Y');
        for ($index = 0; $index <= self::YEARS_RANGE; $index++) {
            $year = $first + $index;
            $years[$year] = $year;
        }
        return $years;
    }

    public function getInfoParcelamentoJuros() {
        $client = new Client($this->getCoreConfig('payment/pagarmeconfig/api_key'));
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cart = $objectManager->get('\Magento\Checkout\Model\Session');

        $max_installments = (int)$this->getCoreConfig('payment/pagarmecc/max_installments');
        $valorminimo = $this->getCoreConfig('payment/pagarmecc/minimo_value_parcel');
        $parcel = 1;
        for ($x = 1;$x <= $max_installments;$x++){
            $final = ($cart->getQuote()->getGrandTotal() / $x);
            if($final >= (int)$valorminimo) {
                $parcel =  $x;
            }
        }

        $calculateInstallments = $client->transactions()->calculateInstallments([
            'amount' =>str_replace(".", "", number_format((float) $cart->getQuote()->getGrandTotal(), 2, '.', '')),
            'free_installments' => $this->getCoreConfig('payment/pagarmecc/free_installments'),
            'max_installments' => $parcel,
            'interest_rate' => floatval(str_replace(',','.',$this->getCoreConfig('payment/pagarmecc/interest_rate')))
        ]);

        return $calculateInstallments;
    }

    public function getCurrencyData()
    {
        $currencySymbol = $this->_priceCurrency
            ->getCurrency()->getCurrencySymbol();
        return $currencySymbol;
    }

    public function getFlagCard()
    {
        $parcelasMinimo = $this->scopeConfig->getValue('payment/pagarmecc/card_type_flag');
        return $parcelasMinimo;
    }
    public function MinInstallment()
    {
        $parcelasMinimo = $this->scopeConfig->getValue('payment/pagarmecc/installments_min');
        return $parcelasMinimo;
    }
    public function interestFrom()
    {
        $parcelasMinimo = $this->scopeConfig->getValue('payment/pagarmecc/interest_from');
        return $parcelasMinimo;
    }
    public function interestParcel()
    {
        $parcelasMinimo = $this->scopeConfig->getValue('payment/pagarmecc/interest_parcel');
        return $parcelasMinimo;
    }
    public function TypeInstallment()
    {
        $parcelasMinimo = $this->scopeConfig->getValue('payment/pagarmecc/type_interest');
        return $parcelasMinimo;
    }
    public function cctypesCard()
    {
        $parcelasMinimo = $this->scopeConfig->getValue('payment/pagarmecc/cctypes');
        return $parcelasMinimo;
    }
    public function MaxInstallment()
    {
        $parcelasMaximo = $this->scopeConfig->getValue('payment/pagarmecc/installments');
        return $parcelasMaximo;
    }
    public function layoutCardView()
    {
        $parcelasMaximo = $this->scopeConfig->getValue('payment/pagarmecc/layout_card_view');
        return $parcelasMaximo;
    }


    public function getCoreConfig($valor){
        $scopeConfig = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\Config\ScopeConfigInterface');
        return $scopeConfig->getValue($valor, \Magento\Store\Model\ScopeInterface::SCOPE_STORES);
    }

}
