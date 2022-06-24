<?php
namespace Blueskytechco\OnePageCheckout\Plugin\Customer\Model\Address\Validator;

use Magento\Sales\Model\Order\Address;
use Blueskytechco\OnePageCheckout\Helper\Address as AddressHelper;
use Magento\Customer\Model\Address\AbstractAddress;

class General
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * Validator constructor.
     *
     * @param Address $helper
     */
    public function __construct(AddressHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param Address\Validator $subject
     * @param Address $address
     *
     * @return array
     */
    public function aroundValidate(\Magento\Customer\Model\Address\Validator\General $subject, \Closure $proceed, AbstractAddress $address)
    {
        if ($this->helper->getModuleStatus()) {
            $fields = $this->helper->getFieldPosition();
            $errors = [];
            foreach ($fields as $field)
            {
                if(isset($field['required']) && $field['required'] && !in_array($field['code'], ['country_id', 'postcode', 'region_id']))
                {
                    if (!\Zend_Validate::is($address->getData($field['code']), 'NotEmpty')) {
                        $errors[] = __('"%fieldName" is required. Enter and try again.', ['fieldName' => $field['code']]);
                    }
                }
            }
            return $errors;
        }
        return $proceed($address);
    }
}
