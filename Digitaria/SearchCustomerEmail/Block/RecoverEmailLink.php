<?php
namespace Digitaria\SearchCustomerEmail\Block;

use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session;

class RecoverEmailLink extends \Magento\Framework\View\Element\Template
{
    protected $customerSession;

    public function __construct(
        Context $context,
        Session $customerSession,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerSession = $customerSession;
    }

    /**
     * Get the URL for the custom forgot password page
     *
     * @return string
     */
    public function getRecoverEmailUrl()
    {
        return $this->getUrl('searchcustomeremail/recoveremail/recover');
    }

    /**
     * Get the label for the custom forgot password link
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function getLabel()
    {
        return __('Forgot Email');
    }
}
