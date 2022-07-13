<?php


namespace MestreMage\Cielo\Observer\Sales;

class OrderInvoicePay implements \Magento\Framework\Event\ObserverInterface
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
        $invoice_data = $observer->getEvent()->getInvoice();
        $payment = $invoice_data->getOrder()->getPayment();
        
        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        $cielo_status = $objectManager->get('MestreMage\Cielo\Helper\Data');
        
        if($payment->getAdditionalInformation('PaymentId')) {
           $cielo_status->capturePymentCielo($payment->getAdditionalInformation('PaymentId'), $payment->getAmountOrdered());
        }
    }
}