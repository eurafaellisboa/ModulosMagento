<?php


namespace Magento\ItauPix\Observer\Sales;

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
        $paymentMethod = $payment->getMethod();

        if(in_array($paymentMethod, ['itaupixbl', 'itaupix'])){
            $order->setState('new')->setStatus('pending')->save();
        }
    }
}