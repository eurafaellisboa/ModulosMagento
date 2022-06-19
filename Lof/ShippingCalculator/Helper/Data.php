<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_ShippingCalculator
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\ShippingCalculator\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Directory\Model\Config\Source\Country;
use Magento\Checkout\Model\CartFactory;
use Magento\Checkout\Model\Cart;
use Magento\Customer\Model\SessionFactory;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\AddressRepositoryInterface;

class Data extends AbstractHelper
{
    const XML_PATH_MODULE_NAME = 'lofshippingcalculator';

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var CurrencyFactory
     */
    protected $currencyFactory;

    /**
     * @var FormatInterface
     */
    protected $localeFormat;

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var CartFactory
     */
    protected $cartFactory;

    /**
     * @var SessionFactory
     */
    protected $sessionFactory;

    /**
     * @var AddressRepositoryInterface
     */
    protected $addressRepositoryInterface;

    /**
     * @var array|null
     */
    protected $countries = null;

    /**
     * @var Cart|null
     */
    protected $cart = null;

    /**
     * @var \Magento\Customer\Model\Customer|null
     */
    protected $customer = null;

    /**
     * @var \Magento\Customer\Api\Data\AddressInterface|null
     */
    protected $defaultShippingAdderss = null;


    /**
     * __construct Helper Data
     * 
     * @param Context
     * @param StoreManagerInterface
     * @param CurrencyFactory $currencyFactory
     * @param FormatInterface $localeFormat
     * @param Registry $coreRegistry
     * @param Country $country
     * @param CartFactory $cartFactory
     * @param SessionFactory $sessionFactory
     * @param AddressRepositoryInterface $addressRepositoryInterface
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        CurrencyFactory $currencyFactory,
        FormatInterface $localeFormat,
        Registry $coreRegistry,
        Country $country,
        CartFactory $cartFactory,
        SessionFactory $sessionFactory,
        AddressRepositoryInterface $addressRepositoryInterface
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->scopeConfig = $context->getScopeConfig();
        $this->localeFormat = $localeFormat;
        $this->currencyFactory = $currencyFactory;
        $this->urlBuilder = $context->getUrlBuilder();
        $this->mediaUrl = $this->urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]);
        $this->country = $country;
        $this->cartFactory = $cartFactory;
        $this->sessionFactory = $sessionFactory;
        $this->addressRepositoryInterface = $addressRepositoryInterface;
    }

    /**
     * Get Store Code
     * @return string
     */
    public function getStoreCode() 
    {
        $store = $this->_storeManager->getStore();
        return $store->getCode();
    }

    /**
     * Get Module config
     * 
     * @param string $key
     * @param int|string|null $store
     * @param string|null $module_path
     * @return string|int|bool|null
     */
    public function getConfig($key, $store = null, $module_path = null)
    {
        $store = $this->_storeManager->getStore($store);
        $module_path = $module_path?$module_path:self::XML_PATH_MODULE_NAME;
        $result = $this->scopeConfig->getValue(
            $module_path."/".$key,
            ScopeInterface::SCOPE_STORE,
            $store
        );
        return $result;
    }

    /**
     * Return module status
     * 
     * @param int|string|null $storeId
     * @return string|int
     */
    public function getEnable($storeId = null)
    {
        return $this->getConfig('general/enable', $storeId);
    }
    
    /**
     * Get media url
     * 
     * @param string|null
     * @return string
     */
    public function getMediaUrl($path = '')
    {
       return $this->mediaUrl . $path;
    }
    
    /**
     * get country options
     * 
     * @return array|mixed|null
     */
    public function getCountryOptionArray()
    {
        if ($this->countries === null) {
            $this->countries = $this->country->toOptionArray();
        }
        return $this->countries;
    }
    
    /**
     * Get price format
     * @return mixed|string
     */
    public function getPriceFormat()
	{
		return $this->localeFormat->getPriceFormat();
	}
	
    /**
     * get model cart
     * 
     * @return Cart|null
     */
    public function getCart()
    {
        if (null === $this->cart) {
            $this->cart = $this->cartFactory->create();
        }
        return $this->cart;
    }
    
    /**
     * get customer session
     * 
     * @return \Magento\Customer\Model\Customer|null
     */
    public function getCustomer()
    {
        if (null === $this->customer) {
            $this->customer = $this->getCustomerSession()->getCustomer();
        }
        return $this->customer;
    }
   
    /**
     * Get default shoipping address
     * 
     * @return \Magento\Customer\Api\Data\AddressInterface|null
     */
    public function getDefaultShippingAddress()
    {
        if (null === $this->defaultShippingAdderss) {
            $this->defaultShippingAdderss = false;
            if ($this->getCustomer()->getId()) {
                $defaultShipping = $this->getCustomer()->getDefaultShipping();
                if ($defaultShipping) {
                    $this->defaultShippingAdderss = $this->addressRepositoryInterface->getById($defaultShipping);                
                }
            }
        }
        return $this->defaultShippingAdderss;
    }

    /**
     * prepareShippingAddress 
     * @param Object|mixed $block
     * @return $this
     */
    public function prepareShippingAddress($block)
    {
        $defaultShippingAdrress = $this->getDefaultShippingAddress();
        if ($defaultShippingAdrress) {
            $block->setCountryId($defaultShippingAdrress->getCountryId());
            $block->setRegion($defaultShippingAdrress->getRegion());
            $block->setRegionId($defaultShippingAdrress->getRegionId());
            $block->setPostCode($defaultShippingAdrress->getPostcode());
        }
        return $this;
    }
    
    /**
     * get use price include tax
     * 
     * @return string|int|bool
     */
    public function usePriceInclucdingTax()
    {
        return $this->getConfig('appearance/use_price_inclucding_tax');
    }
    
    /**
     * Get customer session object
     * 
     * @return Session
     */
    public function getCustomerSession()
    {
        return $this->sessionFactory->create();
    }
}
