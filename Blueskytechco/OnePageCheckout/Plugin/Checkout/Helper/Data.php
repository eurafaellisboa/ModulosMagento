<?php
namespace Blueskytechco\OnePageCheckout\Plugin\Checkout\Helper;
class Data
{
    /**
     * config helper.
     *
     * @var Config
     */
    private $configHelper;

    /**
     * @param Config $configHelper
     */
    public function __construct(
        \Blueskytechco\OnePageCheckout\Helper\Data $configHelper
    ) {
        $this->configHelper = $configHelper;
    }

    /**
     * @param \Magento\Checkout\Helper\Data $subject
     * @param callable $proceed
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterIsDisplayBillingOnPaymentMethodAvailable(
        \Magento\Checkout\Helper\Data $subject,
        $result
    ) {
        if ($this->configHelper->getModuleStatus()) {
            $result = false;
        }
        return $result;
    }
}
