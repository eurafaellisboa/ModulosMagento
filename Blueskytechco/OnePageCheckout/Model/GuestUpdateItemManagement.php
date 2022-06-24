<?php
namespace Blueskytechco\OnePageCheckout\Model;

use Blueskytechco\OnePageCheckout\Api\GuestUpdateItemManagementInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Blueskytechco\OnePageCheckout\Api\UpdateItemManagementInterface;

/**
 * Class GuestUpdateItemManagement
 *
 * @package Blueskytechco\OnePageCheckout\Model
 */
class GuestUpdateItemManagement implements GuestUpdateItemManagementInterface
{
    /**
     * @var \Magento\Quote\Model\QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @var \Blueskytechco\OnePageCheckout\Api\UpdateItemManagementInterface
     */
    private $updateItemManagement;

    /**
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param UpdateItemManagementInterface $updateItemManagement
     */
    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        UpdateItemManagementInterface $updateItemManagement
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->updateItemManagement = $updateItemManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function update($cartId, \Magento\Quote\Api\Data\EstimateAddressInterface $address, $itemId, $qty)
    {
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        $quoteId = (int) $quoteIdMask->getQuoteId();
        return $this->updateItemManagement->update($quoteId, $address, $itemId, $qty);
    }
}
