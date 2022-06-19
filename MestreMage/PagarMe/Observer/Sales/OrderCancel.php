<?php


namespace MestreMage\PagarMe\Observer\Sales;
use MestreMage\PagarMe\Model\PagarMe;

class OrderCancel implements \Magento\Framework\Event\ObserverInterface
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

        $pagar_me = new PagarMe();
        $payment = $observer->getEvent()->getPayment();
        if($payment->getMethod() != 'pagarmebl'){
            if($payment->getAdditionalInformation('pagarme_transactions_id')) {
                $pagar_me->cancelPymentPagarMe($payment->getAdditionalInformation('pagarme_transactions_id'));
            }
        }
    }
}