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

namespace Lof\ShippingCalculator\Model\Data;

use Lof\ShippingCalculator\Api\Data\ShippingRateInterface;

class ShippingRate extends \Magento\Framework\Api\AbstractExtensibleObject implements ShippingRateInterface
{
    /**
     * {@inheritdoc}
     */
    public function getCarrierCode()
    {
        return $this->_get(self::CARRIER_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCarrierCode($carrier_code)
    {
        return $this->setData(self::CARRIER_CODE, $carrier_code);
    }

    /**
     * {@inheritdoc}
     */
    public function getMethodCode()
    {
        return $this->_get(self::METHOD_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setMethodCode($method_code)
    {
        return $this->setData(self::METHOD_CODE, $method_code);
    }

    /**
     * {@inheritdoc}
     */
    public function getCarrierTitle() 
    {
        return $this->_get(self::CARRIER_TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCarrierTitle($carrier_title)
    {
        return $this->setData(self::CARRIER_TITLE, $carrier_title);
    }

    /**
     * {@inheritdoc}
     */
    public function getMethodTitle()
    {
        return $this->_get(self::METHOD_TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setMethodTitle($method_title)
    {
        return $this->setData(self::METHOD_TITLE, $method_title);
    }

    /**
     * {@inheritdoc}
     */
    public function getAmount()
    {
        return $this->_get(self::AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setAmount($amount)
    {
        return $this->setData(self::AMOUNT, $amount);
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailable()
    {
        return $this->_get(self::AVAILABLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setAvailable($available)
    {
        return $this->setData(self::AVAILABLE, $available);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseAmount()
    {
        return $this->_get(self::BASE_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseAmount($base_amount)
    {
        return $this->setData(self::BASE_AMOUNT, $base_amount);
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorMessage()
    {
        return $this->_get(self::ERROR_MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setErrorMessage($error_message)
    {
        return $this->setData(self::ERROR_MESSAGE, $error_message);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriceExclTax()
    {
        return $this->_get(self::PRICE_EXCL_TAX);
    }

    /**
     * {@inheritdoc}
     */
    public function setPriceExclTax($price_excl_tax)
    {
        return $this->setData(self::PRICE_EXCL_TAX, $price_excl_tax);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriceInclTax()
    {
        return $this->_get(self::PRICE_INCL_TAX);
    }

    /**
     * {@inheritdoc}
     */
    public function setPriceInclTax($price_incl_tax)
    {
        return $this->setData(self::PRICE_INCL_TAX, $price_incl_tax);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsCheapest()
    {
        return $this->_get(self::IS_CHEAPEST);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsCheapest($is_cheapest)
    {
        return $this->setData(self::IS_CHEAPEST, $is_cheapest);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Lof\ShippingCalculator\Api\Data\ShippingRateExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}

