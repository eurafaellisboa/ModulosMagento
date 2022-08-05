<?php


namespace Magento\Getnet\Observer\Sales;

class OrderSaveAfter implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $order = $observer->getEvent()->getOrder();
        $payment = $order->getPayment();

        if($payment->getMethod() == 'magentogn'){
            if($payment->getTxnType() == 'capture'){
                $orderState = \Magento\Sales\Model\Order::STATE_COMPLETE;
                $order->setState($orderState)->setStatus(\Magento\Sales\Model\Order::STATE_COMPLETE);
                $order->save();
            }

        }
    }
}