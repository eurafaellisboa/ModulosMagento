<?php


namespace MestreMage\ItauShopline\Observer\Sales;

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
        $itaushopline_status = $objectManager->get('MestreMage\ItauShopline\Api\Transaction');
        $payment = $observer->getEvent()->getPayment();
        if ($payment instanceof \Magento\Sales\Model\Order\Payment) {
           $retorno = $itaushopline_status->cancelBoleto($payment->getAdditionalInformation('transaction_id'));
            $mensagem = $objectManager->get('Magento\Framework\Message\ManagerInterface');
            $mensagem->addSuccessMessage($retorno);
        }

    }
}