<?php
/**
 *
 */
namespace Magento\ItauPix\Model\Source;

use Magento\Framework\Locale\Bundle\DataBundle;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Escaper;
use Magento\Payment\Model\CcConfig;
use Magento\Framework\View\Asset\Source;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Customer\Model\Session;
use Magento\ItauPix\Api\Client;


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
        'itaupix'
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

                if ($code = 'itaupixbl') {
                    $config['payment'][$code]['instructions_checkout'] = $this->getInstructionsCheckout();
                }

                if ($code = 'itaupix') {
                    $config['payment'][$code]['instructions_checkout_pix'] = $this->getInstructionsCheckoutPix();
                }
            }
        }

        return $config;
    }

    public function getInstructionsCheckout()
    {
        $instructionsCheckout = $this->scopeConfig->getValue('payment/itaupixbl/boleto_instructions_checkout');
        return $instructionsCheckout;
    }
    public function getInstructionsCheckoutPix()
    {
        $instructionsCheckout = $this->scopeConfig->getValue('payment/itaupix/pix_instructions_checkout');
        $instructionsCheckout = str_replace('{{img_pix}}', $this->getPixImg(), $instructionsCheckout);
        return $instructionsCheckout;
    }

    public function getSuccessPageInformation()
    {
        $instructionsCheckout = $this->scopeConfig->getValue('payment/itaupix/success_page_information');
        $instructionsCheckout = str_replace('{{img_pix}}', $this->getPixImg(), $instructionsCheckout);
        return $instructionsCheckout;
    }

    public function getPixImg(){
        $asset = $this->ccConfig
         ->createAsset('Magento_ItauPix::images/pix.svg');
        return $asset->getUrl();
    }
    
    public function getCoreConfig($valor){
        $scopeConfig = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\Config\ScopeConfigInterface');
        return $scopeConfig->getValue($valor, \Magento\Store\Model\ScopeInterface::SCOPE_STORES);
    }

}
