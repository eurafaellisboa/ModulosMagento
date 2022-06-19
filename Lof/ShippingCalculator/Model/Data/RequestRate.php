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

use Lof\ShippingCalculator\Api\Data\RequestRateInterface;

class RequestRate extends \Magento\Framework\Api\AbstractExtensibleObject implements RequestRateInterface
{
    /**
     * {@inheritdoc}
     */
    public function getProduct()
    {
        return $this->_get(self::PRODUCT);
    }

    /**
     * {@inheritdoc}
     */
    public function setProduct($product_id)
    {
        return $this->setData(self::PRODUCT, $product_id);
    }

    /**
     * {@inheritdoc}
     */
    public function getRelatedProduct()
    {
        return $this->_get(self::RELATED_PRODUCT);
    }

    /**
     * {@inheritdoc}
     */
    public function setRelatedProduct($related_product)
    {
        return $this->setData(self::RELATED_PRODUCT, $related_product);
    }

    /**
     * {@inheritdoc}
     */
    public function getSelectedConfigurableOption() 
    {
        return $this->_get(self::SELECTED_CONFIGURABLE_OPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setSelectedConfigurableOption($selected_configurable_option)
    {
        return $this->setData(self::SELECTED_CONFIGURABLE_OPTION, $selected_configurable_option);
    }

    /**
     * {@inheritdoc}
     */
    public function getSuperAttribute()
    {
        return $this->_get(self::SUPER_ATTRIBUTE);
    }

    /**
     * {@inheritdoc}
     */
    public function setSuperAttribute($super_attribute)
    {
        return $this->setData(self::SUPER_ATTRIBUTE, $super_attribute);
    }

    /**
     * {@inheritdoc}
     */
    public function getQty()
    {
        return $this->_get(self::QTY);
    }

    /**
     * {@inheritdoc}
     */
    public function setQty($qty)
    {
        return $this->setData(self::QTY, $qty);
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingData()
    {
        return $this->_get(self::SHIPPING_DATA);
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingData($shipping_data)
    {
        return $this->setData(self::SHIPPING_DATA, $shipping_data);
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
        \Lof\ShippingCalculator\Api\Data\RequestRateExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}

