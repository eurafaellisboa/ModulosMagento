<?php
namespace Blueskytechco\OnePageCheckout\Model;

use Blueskytechco\OnePageCheckout\Api\Data\UpdateItemDetailsInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Class GuestUpdateItemManagement
 *
 * @package Blueskytechco\OnePageCheckout\Model
 */
class UpdateItemDetails extends AbstractExtensibleModel implements UpdateItemDetailsInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPaymentMethods()
    {
        return $this->getData(self::PAYMENT_METHODS);
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentMethods($paymentMethods)
    {
        return $this->setData(self::PAYMENT_METHODS, $paymentMethods);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotals()
    {
        return $this->getData(self::TOTALS);
    }

    /**
     * {@inheritdoc}
     */
    public function setTotals($totals)
    {
        return $this->setData(self::TOTALS, $totals);
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingMethods()
    {
        return $this->getData(self::SHIPPING_METHODS);
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingMethods($shippingMethods)
    {
        return $this->setData(self::SHIPPING_METHODS, $shippingMethods);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return $this->getData(self::MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setMessage($message)
    {
        return $this->setData(self::MESSAGE, $message);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * {@inheritdoc}
     */
    public function getHasError()
    {
        return $this->getData(self::HAS_ERROR);
    }

    /**
     * {@inheritdoc}
     */
    public function setHasError($error)
    {
        return $this->setData(self::HAS_ERROR, $error);
    }

}
