<?php
namespace Magento\BoletoItau\Cron;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Framework\DB\Transaction;


class StatusItau
{
    protected $orderRepository;
    protected $invoiceService;
    protected $transaction;

    public function __construct(
        Context $context,
        OrderRepositoryInterface $orderRepository,
        InvoiceService $invoiceService,
        Transaction $transaction
    ) {
        $this->orderRepository = $orderRepository;
        $this->invoiceService = $invoiceService;
        $this->transaction = $transaction;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        $orderDatamodel = $objectManager->get('Magento\Sales\Model\Order')->getCollection()->setOrder('created_at','DESC');
        foreach($orderDatamodel as $orderDatamodel1) {
            if($order_number_itau_shopline = $orderDatamodel1->getPayment()->getAdditionalInformation('order_number_itau_shopline')){
               if($orderDatamodel1->getData('status') == 'pending'){
                   $itaucripto = new \Magento\BoletoItau\Api\Itaucripto();
                   $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
                   $scopeConfig = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
                   $dc = $itaucripto->geraConsulta(strtoupper($scopeConfig->getValue('payment/boletoitau/company_code', $storeScope)), $order_number_itau_shopline, 01, $scopeConfig->getValue('payment/boletoitau/encrytion_Key', $storeScope));
                   $url = 'https://shopline.itau.com.br/shopline/consulta.aspx?DC='.$dc;
                   $data = array("DC" => $dc);
                   $data_string = json_encode($data);

                   $ch = curl_init($url);
                   curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                   curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                   curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                           'Content-Type: application/json',
                           'Content-Length: ' . strlen($data_string))
                   );

                   $xml = new \SimpleXMLElement(curl_exec($ch));
                   $json = json_encode($xml);
                   $array = json_decode($json,TRUE);

                   $situacao_pag = '';
                   foreach($array['PARAMETER']['PARAM'] as $items){
                       foreach($items as $item){
                           if($item['ID'] == 'sitPag'){
                               $situacao_pag = $item['VALUE'];
                           }
                       }
                   }

                   if($situacao_pag == '00'){
                       $orderStateProcessing = \Magento\Sales\Model\Order::STATE_PROCESSING;
                       $orderDatamodel1->setState($orderStateProcessing)->setStatus($orderStateProcessing);
                       $orderDatamodel1->save();

                       if($scopeConfig->getValue('payment/boletoitau/order_invoice', $storeScope)){
                           $this->setInvoice($orderDatamodel1->getData('entity_id'));
                       }
                   }
               }
            }

        }
    }
    public function setInvoice($orderId){
            $order = $this->orderRepository->get($orderId);
            if($order->canInvoice()) {
                $invoice = $this->invoiceService->prepareInvoice($order);
                $invoice->register();
                $invoice->save();
                $transactionSave = $this->transaction->addObject(
                    $invoice
                )->addObject(
                    $invoice->getOrder()
                );
                $transactionSave->save();
                $this->invoiceSender->send($invoice);
                //Send Invoice mail to customer
                $order->addStatusHistoryComment(
                    __('Notified customer about invoice creation #%1.', $invoice->getId())
                )
                    ->setIsCustomerNotified(true)
                    ->save();
            }

    }

}