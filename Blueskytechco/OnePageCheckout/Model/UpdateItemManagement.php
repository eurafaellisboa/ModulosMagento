<?php
namespace Blueskytechco\OnePageCheckout\Model;

use Blueskytechco\OnePageCheckout\Api\UpdateItemManagementInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Blueskytechco\OnePageCheckout\Api\Data\UpdateItemDetailsInterfaceFactory;
use Magento\Quote\Api\ShippingMethodManagementInterface;
use Magento\Quote\Api\PaymentMethodManagementInterface;
use Magento\Quote\Api\CartTotalRepositoryInterface;
use Magento\Framework\Escaper;

/**
 * Class UpdateItemManagement
 *
 * @package Blueskytechco\OnePageCheckout\Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UpdateItemManagement implements UpdateItemManagementInterface
{
    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var UpdateItemDetailsInterfaceFactory
     */
    private $updateItemDetails;

    /**
     * @var ShippingMethodManagementInterface
     */
    private $shippingMethodManagement;

    /**
     * @var PaymentMethodManagementInterface
     */
    private $paymentMethodManagement;

    /**
     * @var CartTotalRepositoryInterface
     */
    private $cartTotalRepository;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * UpdateItemManagement constructor.
     * @param CartRepositoryInterface $cartRepository
     * @param UpdateItemDetailsInterfaceFactory $updateItemDetails
     * @param ShippingMethodManagementInterface $shippingMethodManagement
     * @param PaymentMethodManagementInterface $paymentMethodManagement
     * @param CartTotalRepositoryInterface $cartTotalRepository
     * @param Escaper $escaper
     */
    public function __construct(
        CartRepositoryInterface $cartRepository,
        UpdateItemDetailsInterfaceFactory $updateItemDetails,
        ShippingMethodManagementInterface $shippingMethodManagement,
        PaymentMethodManagementInterface $paymentMethodManagement,
        CartTotalRepositoryInterface $cartTotalRepository,
        Escaper $escaper
    ) {
        $this->cartRepository = $cartRepository;
        $this->updateItemDetails = $updateItemDetails;
        $this->shippingMethodManagement = $shippingMethodManagement;
        $this->paymentMethodManagement = $paymentMethodManagement;
        $this->cartTotalRepository = $cartTotalRepository;
        $this->escaper = $escaper;
    }

    /**
     * @param int $cartId
     * @param \Magento\Quote\Api\Data\EstimateAddressInterface $address
     * @param int $itemId
     * @param float $qty
     * @return \Blueskytechco\OnePageCheckout\Api\Data\UpdateItemDetailsInterface
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function update(
        $cartId,
        \Magento\Quote\Api\Data\EstimateAddressInterface $address,
        $itemId,
        $qty
    ) {
        $status = false;
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->cartRepository->getActive($cartId);
        if (!$quote) {
            throw new NoSuchEntityException(__('This quote does not exist.'));
        }
        $quoteItem = $quote->getItemById($itemId);
        if (!$quoteItem) {
            throw new NoSuchEntityException(__('We can\'t find the quote item.'));
        }
        try {
            if (!$qty || $qty <= 0) {
                $quote->removeItem($itemId);
                $this->cartRepository->save($quote);
                $status = true;
                $message = __(
                    '%1 was removed in your shopping cart.',
                    $this->escaper->escapeHtml($quoteItem->getProduct()->getName())
                );
            } else {
                $quoteItem->setQty($qty);
                if ($quoteItem->getHasError()) {
                    throw new CouldNotSaveException(__($quoteItem->getMessage()));
                } else {
                    $quoteItem->save();
                    $status = true;
                    $message = __(
                        '%1 was updated in your shopping cart.',
                        $this->escaper->escapeHtml($quoteItem->getProduct()->getName())
                    );
                }
            }
            $this->cartRepository->save($quote);
        } catch (LocalizedException $e) {
            $message = $e->getMessage();
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }
        return $this->getUpdateCartDetails($quote, $address, $cartId, $message, $status);
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\EstimateAddressInterface $address
     * @param int $quoteId
     * @param string $message
     * @param boolean $status
     * @return \Blueskytechco\OnePageCheckout\Api\Data\UpdateItemDetailsInterface
     * @throws NoSuchEntityException
     */
    private function getUpdateCartDetails($quote, $address, $quoteId, $message, $status)
    {
        $cartDetails = $this->updateItemDetails->create();
        $paymentMethods = $this->paymentMethodManagement->getList($quoteId);
        $totals = $this->cartTotalRepository->get($quoteId);
        $shippingAddress = $quote->getShippingAddress();
        if ($shippingAddress && $shippingAddress->getCustomerAddressId()) {
            $shippingMethods = $this->shippingMethodManagement->estimateByAddressId(
                $quoteId,
                $shippingAddress->getCustomerAddressId()
            );
        } else {
            $shippingMethods = $this->shippingMethodManagement->estimateByAddress($quoteId, $address);
        }
        $cartDetails->setShippingMethods($shippingMethods);
        $cartDetails->setPaymentMethods($paymentMethods);
        $cartDetails->setTotals($totals);
        $cartDetails->setMessage($message);
        $cartDetails->setStatus($status);
        if (!$quote->hasItems() || $quote->getHasError() || !$quote->validateMinimumAmount()) {
            $cartDetails->setHasError(true);
        }

        return $cartDetails;
    }
}
