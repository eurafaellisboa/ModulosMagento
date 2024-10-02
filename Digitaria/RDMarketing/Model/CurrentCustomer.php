<?php
namespace Digitaria\RDMarketing\Model;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Stdlib\CookieManagerInterface;

class CurrentCustomer
{
    protected $customerSession;
    protected $cookieManager;

    public function __construct(
        CustomerSession $customerSession,
        CookieManagerInterface $cookieManager
    ) {
        $this->customerSession = $customerSession;
        $this->cookieManager = $cookieManager;
    }

    public function getEmail()
    {
        if ($this->customerSession->isLoggedIn()) {
            return $this->customerSession->getCustomer()->getEmail();
        } else {
            $emailCookie = $this->cookieManager->getCookie('email');
            if ($emailCookie) {
                return $emailCookie;
            }
        }

        return null;
    }
}
