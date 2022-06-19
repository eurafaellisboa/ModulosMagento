<?php
/**
 * Copyright © Landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\ShippingCalculator\Api\Data;

interface ShippingDataInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const PRODUCT_ID = 'product_id';
    const COUNTRY_ID = 'country_id';
    const REGION_ID = 'region_id';
    const REGION = 'region';
    const POSTCODE = 'postcode';
    const QTY = 'qty';

    /**
     * Get product_id
     * @return int|null
     */
    public function getProductId();

    /**
     * Set product_id
     * @param int $product_id
     * @return \Lof\ShippingCalculator\Api\Data\ShippingDataInterface
     */
    public function setProductId($product_id);

    /**
     * Get country_id
     * @return string|null
     */
    public function getCountryId();

    /**
     * Set country_id
     * @param string $country_id
     * @return \Lof\ShippingCalculator\Api\Data\ShippingDataInterface
     */
    public function setCountryId($country_id);

    /**
     * Get region_id
     * @return string|null
     */
    public function getRegionId();

    /**
     * Set region_id
     * @param string $region_id
     * @return \Lof\ShippingCalculator\Api\Data\ShippingDataInterface
     */
    public function setRegionId($region_id);

    /**
     * Get region
     * @return string|null
     */
    public function getRegion();

    /**
     * Set region
     * @param string $region
     * @return \Lof\ShippingCalculator\Api\Data\ShippingDataInterface
     */
    public function setRegion($region);

    /**
     * Get postcode
     * @return string|null
     */
    public function getPostcode();

    /**
     * Set postcode
     * @param string $postcode
     * @return \Lof\ShippingCalculator\Api\Data\ShippingDataInterface
     */
    public function setPostcode($postcode);

    /**
     * Get qty
     * @return float|int|null
     */
    public function getQty();

    /**
     * Set qty
     * @param float|int $qty
     * @return \Lof\ShippingCalculator\Api\Data\ShippingDataInterface
     */
    public function setQty($qty);
    
    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lof\ShippingCalculator\Api\Data\ShippingDataExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Lof\ShippingCalculator\Api\Data\ShippingDataExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Lof\ShippingCalculator\Api\Data\ShippingDataExtensionInterface $extensionAttributes
    );
}