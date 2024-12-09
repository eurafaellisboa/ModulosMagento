<?php

namespace Nuzz\CheckoutDob\Plugin;

use Magento\Quote\Api\CartRepositoryInterface;

class ShippingInformationManagement
{
    /**
     * @var CartRepositoryInterface
     */
    public $cartRepository;

    /**
     * ShippingInformationManagement constructor.
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        CartRepositoryInterface $cartRepository
    ) {
        $this->cartRepository = $cartRepository;
    }

    /**
     * Save custom field data to quote object
     * @param $subject
     * @param $cartId
     * @param $addressInformation
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeSaveAddressInformation($subject, $cartId, $addressInformation)
    {
        $quote = $this->cartRepository->getActive($cartId);
        $dob = $addressInformation->getShippingAddress()->getExtensionAttributes()->getDob();
        $quote->setDob($dob);
        $this->cartRepository->save($quote);
        return [$cartId, $addressInformation];
    }
}
