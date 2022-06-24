<?php
namespace Blueskytechco\OnePageCheckout\Observer\Admin;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\LayoutInterface;

class CoreLayoutRenderElementObserver implements ObserverInterface
{
    /**
     * @var LayoutInterface
     */
    private $layout;
    /**
     * One step checkout config helper
     *
     * @var Config
     */
    private $configHelper;

    public function __construct(
        LayoutInterface $layout,
        \Blueskytechco\OnePageCheckout\Helper\Data $configHelper
    ) {
        $this->layout = $layout;
        $this->configHelper = $configHelper;
    }

    /**
     * Execute observer
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        if ($observer->getElementName() == 'order_shipping_view' &&
            $this->configHelper->getModuleStatus() ) {
            $orderShippingViewBlock = $observer->getLayout()->getBlock($observer->getElementName());
            $order = $orderShippingViewBlock->getOrder();
            $deliveryBlock = $this->layout->createBlock(\Magento\Framework\View\Element\Template::class);
            $date = $order->getCustomerShippingDate();
            $deliveryBlock->setCustomerShippingDate($date)
                ->setCustomerShippingComments($order->getCustomerShippingComments())
                ->setActiveJs(true)
                ->setTemplate('Blueskytechco_OnePageCheckout::delivery.phtml');
            $html = $observer->getTransport()->getOutput() . $deliveryBlock->toHtml();
            $observer->getTransport()->setOutput($html);
        }
    }
}
