<?php

namespace Magento\Getnet\Model;

use Magento\Framework\Locale\Bundle\DataBundle;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Escaper;
use Magento\Payment\Model\CcConfig;
use Magento\Framework\View\Asset\Source;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Customer\Model\Session;


class ConfigProviderGn implements ConfigProviderInterface
{
	/**
     * Years range
     */
    const YEARS_RANGE = 20;
    /**
     * @var string[]
     */
    protected $methodCodes = [
        'magentogn'
    ];

	protected $_ccoptions = [
        'mastercard' => 'Mastercard',
        'visa' => 'Visa',
        'amex' => 'American Express',
		'diners' => 'Diners',
        'elo' => 'Elo',
        'hipercard' => 'Hipercard',
		'hiper' => 'HIPER'
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
		\Magento\Framework\Pricing\Helper\Data $priceFilter,
        \Magento\Getnet\Helper\Data $magentoHelper
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
        $this->_magentoHelper = $magentoHelper;
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
                $config['payment'][$code]['homologation_mode_enabled'] = $this->homologationModeEnabled();
                $config['payment'][$code]['cardmemory'] = $this->getCardGetnet();
                $config['payment'][$code]['accepts_save_card'] = $this->getAcceptsCard();
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
                    ->createAsset('Magento_Getnet::images/cc/cvv.gif');
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
                $asset = $this->ccConfig
                    ->createAsset('Magento_Getnet::images/cc/' . strtolower($code) . '.png');
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
        $juros = [];

        $juros['0'] = 0;
		$juros['1'] = 0;


        for ($i = 1; $i <= (int)$this->MaxInstallment(); $i++) {
            if($i >= (int)$this->interestFrom())
                $juros[$i] = (float)$this->interestParcel();
            else
                $juros[$i] = 0;

        }

        return $juros;
    }

	public function getCurrencyData()
    {
        $currencySymbol = $this->_priceCurrency
            ->getCurrency()->getCurrencySymbol();
        return $currencySymbol;
    }

    public function getCardGetnet()
    {
        return $this->_magentoHelper->getCardGetnetDecrypt();
    }

	public function MinInstallment()
    {
        $parcelasMinimo = $this->scopeConfig->getValue('payment/magentogn/installments_min');
        return $parcelasMinimo;
    }
    public function interestFrom()
    {
        $parcelasMinimo = $this->scopeConfig->getValue('payment/magentogn/interest_from');
        return $parcelasMinimo;
    }
    public function interestParcel()
    {
        $parcelasMinimo = $this->scopeConfig->getValue('payment/magentogn/interest_parcel');
        return $parcelasMinimo;
    }
    public function TypeInstallment()
    {
        $parcelasMinimo = $this->scopeConfig->getValue('payment/magentogn/type_interest');
        return $parcelasMinimo;
    }
    public function cctypesCard()
    {
        $parcelasMinimo = $this->scopeConfig->getValue('payment/magentogn/cctypes');
        return $parcelasMinimo;
    }
	public function MaxInstallment()
    {
        $parcelasMaximo = $this->scopeConfig->getValue('payment/magentogn/installments');
        return $parcelasMaximo;
    }
    public function layoutCardView()
    {
        $parcelasMaximo = $this->scopeConfig->getValue('payment/magentogn/layout_card_view');
        return $parcelasMaximo;
    }
    public function getAcceptsCard()
    {
        $save_credit_card = 0;
        if($this->_customerSession->isLoggedIn()) {
            $save_credit_card = $this->scopeConfig->getValue('payment/magentogn/save_credit_card');
        }
        return $save_credit_card;
    }

    public function homologationModeEnabled()
    {
        $parcelasMaximo = $this->scopeConfig->getValue('payment/magentogn/homologation_mode_enabled');

        if($parcelasMaximo){
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $cart = $objectManager->get('\Magento\Checkout\Model\Session');
                $items = $cart->getQuote()->getAllItems();
                $veriSku = [];
                foreach($items as $item) {
                     $veriSku[] = $item->getSku();
                }

                if(in_array('getnet_test',$veriSku)){
                    $retorno = true;
                }else{
                    $retorno = false;
                }
            }else{
            $retorno = true;
        }

        return $retorno;
    }


}
