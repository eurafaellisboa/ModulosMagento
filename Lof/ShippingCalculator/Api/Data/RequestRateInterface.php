<?php
/**
 * Copyright © Landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\ShippingCalculator\Api\Data;

interface RequestRateInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const PRODUCT = 'product';
    const RELATED_PRODUCT = 'related_product';
    const SELECTED_CONFIGURABLE_OPTION = 'selected_configurable_option';
    const SUPER_ATTRIBUTE = 'super_attribute';
    const QTY = 'qty';
    const SHIPPING_DATA = 'shipping_data';

    /**
     * Get product_id
     * @return int|null
     */
    public function getProduct();

    /**
     * Set product_id
     * @param int $product_id
     * @return \Lof\ShippingCalculator\Api\Data\RequestRateInterface
     */
    public function setProduct($product_id);

    /**
     * Get related_product
     * @return string|null
     */
    public function getRelatedProduct();

    /**
     * Set related_product
     * @param string $related_product
     * @return \Lof\ShippingCalculator\Api\Data\RequestRateInterface
     */
    public function setRelatedProduct($related_product);

    /**
     * Get selected_configurable_option
     * @return string|null
     */
    public function getSelectedConfigurableOption();

    /**
     * Set selected_configurable_option
     * @param string $selected_configurable_option
     * @return \Lof\ShippingCalculator\Api\Data\RequestRateInterface
     */
    public function setSelectedConfigurableOption($selected_configurable_option);
    
    /**
     * Get super_attribute
     * @return string|null
     */
    public function getSuperAttribute();

    /**
     * Set super_attribute
     * @param string $super_attribute
     * @return \Lof\ShippingCalculator\Api\Data\RequestRateInterface
     */
    public function setSuperAttribute($super_attribute);

    /**
     * Get qty
     * @return float|int|null
     */
    public function getQty();

    /**
     * Set qty
     * @param float|int $qty
     * @return \Lof\ShippingCalculator\Api\Data\RequestRateInterface
     */
    public function setQty($qty);

    /**
     * Get shipping_data
     * @return \Lof\ShippingCalculator\Api\Data\ShippingDataInterface|null
     */
    public function getShippingData();

    /**
     * Set shipping_data
     * @param \Lof\ShippingCalculator\Api\Data\ShippingDataInterface|null
     * @return \Lof\ShippingCalculator\Api\Data\RequestRateInterface
     */
    public function setShippingData($shipping_data);
    
    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lof\ShippingCalculator\Api\Data\RequestRateExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Lof\ShippingCalculator\Api\Data\RequestRateExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Lof\ShippingCalculator\Api\Data\RequestRateExtensionInterface $extensionAttributes
    );
}