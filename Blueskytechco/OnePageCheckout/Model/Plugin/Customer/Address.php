<?php
namespace Blueskytechco\OnePageCheckout\Model\Plugin\Customer;

use Magento\Customer\Api\Data\AddressInterface;

class Address
{
    /**
     * @param \Magento\Customer\Model\Address $subject
     * @param \Magento\Customer\Model\Address $result
     *
     * @return \Magento\Customer\Model\Address
     */
    public function afterUpdateData(\Magento\Customer\Model\Address $subject, $result)
    {
        $result->setShouldIgnoreValidation(true);
        return $result;
    }
}
