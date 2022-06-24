<?php
namespace Blueskytechco\OnePageCheckout\Plugin\Checkout\Model;

use Blueskytechco\OnePageCheckout\Model\AdditionalData;
use Magento\Quote\Api\CartRepositoryInterface;

class CustomerAdditionalData
{
    /**
     * @var AdditionalData
     */
    private $additionalDataModel;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var Magento\Checkout\Model\SessionFactory
     */
    private $checkoutSession;

    /**
     * One step checkout helper
     *
     * @var Config
     */
    private $configHelper;

    /**
     * CustomerAdditionalData constructor.
     * @param AdditionalData $additionalDataModel
     * @param CartRepositoryInterface $cartRepository
     * @param \Magento\Checkout\Model\SessionFactory $checkoutSession
     * @param Config $configHelper
     */
    public function __construct(
        AdditionalData $additionalDataModel,
        CartRepositoryInterface $cartRepository,
        \Magento\Checkout\Model\SessionFactory $checkoutSession,
        \Blueskytechco\OnePageCheckout\Helper\Data $configHelper
    ) {
        $this->additionalDataModel = $additionalDataModel;
        $this->cartRepository = $cartRepository;
        $this->checkoutSession = $checkoutSession;
        $this->configHelper = $configHelper;
    }
    public function aroundSavePaymentInformationAndPlaceOrder(
        \Magento\Checkout\Api\PaymentInformationManagementInterface $subject,
        \Closure $proceed,
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        if ($paymentMethod->getExtensionAttributes() !== null
            && $this->configHelper->getModuleStatus()
            && $paymentMethod->getExtensionAttributes()->getBlueskytechcoOpc() !== null
        ) {
            $additionalData = $paymentMethod->getExtensionAttributes()->getBlueskytechcoOpc();
            $orderId = $proceed($cartId, $paymentMethod, $billingAddress);
            if (!empty($additionalData) && isset($additionalData['order_comment'])) {
                $this->additionalDataModel->saveComment($orderId, $additionalData);
            }
            if (!empty($additionalData)
                && $this->configHelper->isDisplayField('show_subscribe_newsletter')
            ) {
                $this->additionalDataModel->subscriber($orderId, $additionalData);
            }
        } else {
            return $proceed($cartId, $paymentMethod, $billingAddress);
        }
    }
    public function beforeSavePaymentInformation(
        \Magento\Checkout\Api\PaymentInformationManagementInterface $subject,
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        if ($paymentMethod->getExtensionAttributes() !== null
            && $this->configHelper->getModuleStatus()
            && $paymentMethod->getExtensionAttributes()->getBlueskytechcoOpc() !== null
        ) {
            $additionalData = $paymentMethod->getExtensionAttributes()->getBlueskytechcoOpc();
            $quote = $this->cartRepository->getActive($cartId);
            if (!empty($additionalData)) {
                $this->additionalDataModel->saveDelivery($quote, $additionalData);
                if (in_array($paymentMethod->getMethod(), $this->configHelper->getPaymentOnlineMethods())) {
                    $this->checkoutSession->create()->setBlueskytechcoOpcAdditionalData($additionalData);
                }
            }
        }
    }
}
