<?php


namespace MestreMage\Cielo\Cron;

class Cronjob
{

    protected $logger;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(\Psr\Log\LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        $from = date('Y-m-d h:i:s', strtotime('-30 day', strtotime(date("Y-m-d h:i:s"))));
        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        $cielo_status = $objectManager->get('MestreMage\Cielo\Helper\Data');
        $orderDatamodel = $objectManager->get('Magento\Sales\Model\Order')->getCollection()
            ->setOrder('created_at','DESC')
            ->addFieldToFilter('created_at', array('from'=>$from));

        foreach($orderDatamodel as $orderDatamodel1) {
            $adictionalInfo = $orderDatamodel1->getPayment()->getAdditionalInformation();
            if(isset($adictionalInfo['PaymentId'])){
                $status_cielo = $cielo_status->consultPymentCielo($adictionalInfo['PaymentId']);
                $orderState = 'new';
                $orderStatus = 'pending';
                switch ($status_cielo) {
                    case 1:
                    case 2:
                         $orderState = \Magento\Sales\Model\Order::STATE_PROCESSING;
                         $orderStatus = $orderState;            
                         if($status_cielo == 1 && $orderDatamodel1->getPayment()->getMethod() == 'mestremagebl'){
                            $orderState = 'new';
                            $orderStatus = 'pending';
                         }
                         if($status_cielo == 2 && $orderDatamodel1->getPayment()->getMethod() == 'mestremagebl'){
                            $this->createInvoicer($orderDatamodel1);
                        }
                        break;
                    case 3:
                    case 10:
                    case 11:
                    case 13:
                        $orderState = \Magento\Sales\Model\Order::STATE_CANCELED;
                        $orderStatus = $orderState;
                        break;
                    case 12:
                    case 0:
                        $orderStatus = 'pending';
                        $orderState = 'new';
                        break;
                }
                
                if($orderState && $orderDatamodel1->getState() != $orderState){
                    $orderDatamodel1->setState($orderState)->setStatus($orderStatus)->save();
                }

            }
        }

    }

    public function createInvoicer($order){
        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        if ($order->canInvoice()) {
        $invoice = $objectManager->create('Magento\Sales\Model\Service\InvoiceService')->prepareInvoice($order);
        if (!$invoice->getTotalQty()) {
            throw new \Magento\Framework\Exception\LocalizedException( __('You can\'t create an invoice without products.'));
        }
        $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_OFFLINE);
        $transaction = $objectManager->create('Magento\Framework\DB\Transaction')->addObject($invoice)->addObject($invoice->getOrder());
        $transaction->save();
        
        $order->addStatusHistoryComment( __('Notified customer about invoice #%1.', $invoice->getId()))->setIsCustomerNotified(true)->save();
        }
    }
}
