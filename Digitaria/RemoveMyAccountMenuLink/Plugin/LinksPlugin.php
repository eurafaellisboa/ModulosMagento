<?php
namespace Digitaria\RemoveMyAccountMenuLink\Plugin;


class LinksPlugin
{
	
protected $scopeConfig;

    /**
     * AdminFailed constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->scopeConfig = $scopeConfig;
    }	
	
    public function afterRenderLink(\Magento\Framework\View\Element\Html\Links $subject, $result, \Magento\Framework\View\Element\AbstractBlock $link)
    {
		
		$removemyaccount = $this->scopeConfig->getValue('removemyaccountmenulink/general/removemyaccount', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$removemyorders = $this->scopeConfig->getValue('removemyaccountmenulink/general/removemyorders', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$removemydownloads = $this->scopeConfig->getValue('removemyaccountmenulink/general/removemydownloads', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$removelmywishlist = $this->scopeConfig->getValue('removemyaccountmenulink/general/removelmywishlist', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$removemyaddress = $this->scopeConfig->getValue('removemyaccountmenulink/general/removemyaddress', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$removeaccountinformation = $this->scopeConfig->getValue('removemyaccountmenulink/general/removeaccountinformation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$removemypayments = $this->scopeConfig->getValue('removemyaccountmenulink/general/removemypayments', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$removemyreview = $this->scopeConfig->getValue('removemyaccountmenulink/general/removemyreview', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$removenewsletter = $this->scopeConfig->getValue('removemyaccountmenulink/general/removenewsletter', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$removesubscriptions = $this->scopeConfig->getValue('removemyaccountmenulink/general/removesubscriptions', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$removecards = $this->scopeConfig->getValue('removemyaccountmenulink/general/removecards', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$removecartsave = $this->scopeConfig->getValue('removemyaccountmenulink/general/removecartsave', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		
		//My Account
        if(($link->getNameInLayout()=='customer-account-navigation-account-link') && ($removemyaccount == 1))
        {
			
            $result = "";
        }
		
		//My Orders
        if(($link->getNameInLayout()=='customer-account-navigation-orders-link') && ($removemyorders == 1))
        {
			
            $result = "";
        }
		
		//My Downloads
        if(($link->getNameInLayout()=='customer-account-navigation-downloadable-products-link') && ($removemydownloads == 1))
        {
			
            $result = "";
        }
		
		//My Wishlist
        if(($link->getNameInLayout()=='customer-account-navigation-wish-list-link') && ($removelmywishlist == 1))
        {
			
            $result = "";
        }
		
		//My Address
        if(($link->getNameInLayout()=='customer-account-navigation-address-link') && ($removemyaddress == 1))
        {
			
            $result = "";
        }
		
		//My Account Information
        if(($link->getNameInLayout()=='customer-account-navigation-account-edit-link') && ($removeaccountinformation == 1))
        {
			
            $result = "";
        }
		
		//My Payments
        if(($link->getNameInLayout()=='customer-account-navigation-my-credit-cards-link') && ($removemypayments == 1))
        {
			
            $result = "";
        }
		
		//My Reviews
        if(($link->getNameInLayout()=='customer-account-navigation-product-reviews-link') && ($removemyreview == 1))
        {
			
            $result = "";
        }
		
		//My Newsletter
        if(($link->getNameInLayout()=='customer-account-navigation-newsletter-subscriptions-link') && ($removenewsletter == 1))
        {
			
            $result = "";
        }
		
		//My Subscription (pagar.me)
        if(($link->getNameInLayout()=='customer-account-navigation-subscribe') && ($removesubscriptions == 1))
        {
            $result = "";
        }
		
		//My Cards (pagar.me)
        if(($link->getNameInLayout()=='customer-account-navigation-about-me') && ($removecards == 1))
        {
            $result = "";
        }
		
		//My Saved Cart (sharecart)
        if(($link->getNameInLayout()=='customer-account-navigation-mycard') && ($removecartsave == 1))
        {
            $result = "";
        }
		
		
		
		
		
		
		
        return $result;
    }

}
