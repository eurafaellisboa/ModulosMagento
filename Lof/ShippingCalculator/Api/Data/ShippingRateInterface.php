<?php
/**
 * Copyright © Landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\ShippingCalculator\Api\Data;

interface ShippingRateInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const CARRIER_CODE = 'carrier_code';
    const METHOD_CODE = 'method_code';
    const CARRIER_TITLE = 'carrier_title';
    const METHOD_TITLE = 'method_title';
    const AMOUNT = 'amount';
    const AVAILABLE = 'available';
    const BASE_AMOUNT = 'base_amount';
    const ERROR_MESSAGE = 'error_message';
    const PRICE_EXCL_TAX = 'price_excl_tax';
    const PRICE_INCL_TAX = 'price_incl_tax';
    const IS_CHEAPEST = 'is_cheapest';

    /**
     * Get carrier_code
     * @return string|null
     */
    public function getCarrierCode();

    /**
     * Set carrier_code
     * @param string $carrier_code
     * @return \Lof\ShippingCalculator\Api\Data\ShippingRateInterface
     */
    public function setCarrierCode($carrier_code);

    /**
     * Get method_code
     * @return string|null
     */
    public function getMethodCode();

    /**
     * Set method_code
     * @param string $method_code
     * @return \Lof\ShippingCalculator\Api\Data\ShippingRateInterface
     */
    public function setMethodCode($method_code);

    /**
     * Get carrier_title
     * @return string|null
     */
    public function getCarrierTitle();

    /**
     * Set carrier_title
     * @param string $carrier_title
     * @return \Lof\ShippingCalculator\Api\Data\ShippingRateInterface
     */
    public function setCarrierTitle($carrier_title);

    /**
     * Get method_title
     * @return string|null
     */
    public function getMethodTitle();

    /**
     * Set method_title
     * @param string $method_title
     * @return \Lof\ShippingCalculator\Api\Data\ShippingRateInterface
     */
    public function setMethodTitle($method_title);

    /**
     * Get amount
     * @return float|int|null
     */
    public function getAmount();

    /**
     * Set amount
     * @param float|int $amount
     * @return \Lof\ShippingCalculator\Api\Data\ShippingRateInterface
     */
    public function setAmount($amount);

    /**
     * Get available
     * @return bool|int|null
     */
    public function getAvailable();

    /**
     * Set available
     * @param bool|int $available
     * @return \Lof\ShippingCalculator\Api\Data\ShippingRateInterface
     */
    public function setAvailable($available);

    /**
     * Get base_amount
     * @return float|int|null
     */
    public function getBaseAmount();

    /**
     * Set base_amount
     * @param float|int $base_amount
     * @return \Lof\ShippingCalculator\Api\Data\ShippingRateInterface
     */
    public function setBaseAmount($base_amount);

    /**
     * Get error_message
     * @return string|null
     */
    public function getErrorMessage();

    /**
     * Set error_message
     * @param string $error_message
     * @return \Lof\ShippingCalculator\Api\Data\ShippingRateInterface
     */
    public function setErrorMessage($error_message);

    /**
     * Get price_excl_tax
     * @return float|int|null
     */
    public function getPriceExclTax();

    /**
     * Set price_excl_tax
     * @param float|int $price_excl_tax
     * @return \Lof\ShippingCalculator\Api\Data\ShippingRateInterface
     */
    public function setPriceExclTax($price_excl_tax);

    /**
     * Get price_incl_tax
     * @return float|int|null
     */
    public function getPriceInclTax();

    /**
     * Set price_incl_tax
     * @param float|int $price_incl_tax
     * @return \Lof\ShippingCalculator\Api\Data\ShippingRateInterface
     */
    public function setPriceInclTax($price_incl_tax);

    /**
     * Get is_cheapest
     * @return bool|int|null
     */
    public function getIsCheapest();

    /**
     * Set is_cheapest
     * @param bool|int $is_cheapest
     * @return \Lof\ShippingCalculator\Api\Data\ShippingRateInterface
     */
    public function setIsCheapest($is_cheapest);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lof\ShippingCalculator\Api\Data\ShippingRateExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Lof\ShippingCalculator\Api\Data\ShippingRateExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Lof\ShippingCalculator\Api\Data\ShippingRateExtensionInterface $extensionAttributes
    );
}