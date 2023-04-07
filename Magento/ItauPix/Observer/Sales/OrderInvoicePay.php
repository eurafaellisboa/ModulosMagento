<?php


namespace Magento\ItauPix\Observer\Sales;
use Magento\ItauPix\Model\ItauPix;

class OrderInvoicePay implements \Magento\Framework\Event\ObserverInterface
{


    protected $request;

    public function __construct(
        \Magento\Framework\App\Request\Http $request
    ){
        $this->request = $request;
    }


    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $itaupix = new ItauPix();

        $invoice_data = $observer->getEvent()->getInvoice();
        $payment = $invoice_data->getOrder()->getPayment();

        if($this->request->getFullActionName() != 'checkout_onepage_success') {
            if($payment->getAdditionalInformation('itaupix_transactions_id') && !in_array($payment->getMethod(), ['itaupixbl', 'itaupix'])) {
                $itaupix_transactions_id = $payment->getAdditionalInformation('itaupix_transactions_id');
                $status_itaupix =  $itaupix->consultPymentItauPix($itaupix_transactions_id);
                if($status_itaupix != 'CONCLUIDA'){
                    $itaupix->capturedTransaction($itaupix_transactions_id, $payment->getAmountOrdered());
                }
            }
        }
    }
}