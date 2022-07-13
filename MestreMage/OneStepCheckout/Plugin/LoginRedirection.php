<?php
namespace MestreMage\OneStepCheckout\Plugin;

class LoginRedirection
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession
    )
    {
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
    }
    public function afterLoadCustomerQuote(
        \Magento\Checkout\Model\Session $subject,
        $result
    )
    {
        $quote = $subject->getQuote();
        if(count($quote->getAllItems())>0){
            $this->customerSession
                ->setBeforeAuthUrl($this->storeManager->getStore()->getUrl('checkout/'));
        }

    }
}