<?php


namespace MestreMage\Cielo\Observer\Sales;

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

        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        $cielo_status = $objectManager->get('MestreMage\Cielo\Helper\Data');

        $payment = $observer->getEvent()->getPayment();
        if($payment->getAdditionalInformation('PaymentId')) {
            if ($payment instanceof \Magento\Sales\Model\Order\Payment) {

                $cielo_status->cancelPymentCielo($payment->getAdditionalInformation('PaymentId'), $payment->getAmountOrdered());

            }
        }
    }
}