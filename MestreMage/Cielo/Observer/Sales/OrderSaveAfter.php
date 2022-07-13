<?php


namespace MestreMage\Cielo\Observer\Sales;

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
        if($paymentMethod == 'mestremagebl' || $paymentMethod == 'mestremagedc'){
            $order->setState('new')->setStatus('pending')->save();
        }
        
        if($payment->getAdditionalInformation('cielo_transactions_refused')){
            $order->setState('canceled')->setStatus('canceled')->save();
        }
    }
}