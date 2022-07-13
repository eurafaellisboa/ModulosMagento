<?php


namespace MestreMage\ItauShopline\Observer\Sales;

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

        if($paymentMethod == 'itaushopline'){
            $order->setState('new')->setStatus('pending');
            $order->save();
        }
    }
}