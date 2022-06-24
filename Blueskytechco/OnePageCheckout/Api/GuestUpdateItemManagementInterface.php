<?php
namespace Blueskytechco\OnePageCheckout\Api;

/**
 * Interface GuestUpdateItemManagementInterface
 * @api
 */
interface GuestUpdateItemManagementInterface
{
    /**
     * @param string $cartId
     * @param \Magento\Quote\Api\Data\EstimateAddressInterface $address
     * @param int $itemId
     * @param float $qty
     * @return \Blueskytechco\OnePageCheckout\Api\Data\UpdateItemDetailsInterface
     */
    public function update($cartId, \Magento\Quote\Api\Data\EstimateAddressInterface $address, $itemId, $qty);
}
