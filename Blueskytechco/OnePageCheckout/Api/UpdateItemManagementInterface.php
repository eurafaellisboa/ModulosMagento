<?php
namespace Blueskytechco\OnePageCheckout\Api;

/**
 * Interface UpdateItemManagementInterface
 * @api
 */
interface UpdateItemManagementInterface
{
    /**
     * @param int $cartId
     * @param \Magento\Quote\Api\Data\EstimateAddressInterface $address
     * @param int $itemId
     * @param float $qty
     * @return \Blueskytechco\OnePageCheckout\Api\Data\UpdateItemDetailsInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function update($cartId, \Magento\Quote\Api\Data\EstimateAddressInterface $address, $itemId, $qty);
}
