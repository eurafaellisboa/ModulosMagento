<?php
namespace Blueskytechco\OnePageCheckout\Model\Plugin\Eav\Model\Attribute;

use Magento\Framework\Exception\LocalizedException;
use Blueskytechco\OnePageCheckout\Helper\Address;

/**
 * Class AbstractData
 * @package Blueskytechco\OnePageCheckout\Model\Plugin\Eav\Model\Attribute
 */
class AbstractData
{
    /**
     * @var Address
     */
    private $helper;

    /**
     * AbstractData constructor.
     *
     * @param Address $helper
     */
    public function __construct(Address $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Eav\Model\Attribute\Data\AbstractData $subject
     * @param array|string $value
     *
     * @return array|string
     * @throws LocalizedException
     */
    public function beforeValidateValue(\Magento\Eav\Model\Attribute\Data\AbstractData $subject, $value)
    {
        if ($value === null) {
            $value = '';
        }

        $attribute = $subject->getAttribute();

        foreach ($this->helper->getFieldPosition() as $item) {
            if ($item['code'] === $attribute->getAttributeCode()) {
                if (empty($item['required'])) {
                    $attribute->setIsRequired(false);
                }

                return [$value];
            }
        }

        $attribute->setIsRequired(false);

        return [$value];
    }
}
