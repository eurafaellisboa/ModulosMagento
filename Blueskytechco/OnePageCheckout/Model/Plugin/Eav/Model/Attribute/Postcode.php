<?php
namespace Blueskytechco\OnePageCheckout\Model\Plugin\Eav\Model\Attribute;

use Magento\Framework\Exception\LocalizedException;
use Blueskytechco\OnePageCheckout\Helper\Address;

/**
 * Class Postcode
 * @package Blueskytechco\OnePageCheckout\Model\Plugin\Eav\Model\Attribute
 */
class Postcode
{
    /**
     * @var Address
     */
    private $helper;

    /**
     * Postcode constructor.
     *
     * @param Address $helper
     */
    public function __construct(Address $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Customer\Model\Attribute\Data\Postcode $subject
     * @param array|bool $result
     *
     * @return array|string
     * @throws LocalizedException
     */
    public function afterValidateValue(\Magento\Customer\Model\Attribute\Data\Postcode $subject, $result)
    {
        $attribute = $subject->getAttribute();

        foreach ($this->helper->getFieldPosition() as $item) {
            if ($item['code'] === $attribute->getAttributeCode()) {
                if (empty($item['required'])) {
                    return true;
                }

                return $result;
            }
        }

        return true;
    }
}
