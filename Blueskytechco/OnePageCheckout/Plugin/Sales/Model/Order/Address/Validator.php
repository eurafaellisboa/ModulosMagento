<?php
namespace Blueskytechco\OnePageCheckout\Plugin\Sales\Model\Order\Address;

use Magento\Sales\Model\Order\Address;
use Blueskytechco\OnePageCheckout\Helper\Data;


class Validator
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * Validator constructor.
     *
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param Address\Validator $subject
     * @param Address $address
     *
     * @return array
     */
    public function beforeValidateForCustomer(Address\Validator $subject, Address $address)
    {
        if ($this->helper->getModuleStatus()) {
            $address->setShouldIgnoreValidation(true);
        }
        return [$address];
    }
}
