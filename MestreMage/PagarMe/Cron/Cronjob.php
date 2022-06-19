<?php


namespace MestreMage\PagarMe\Cron;
use MestreMage\PagarMe\Model\PagarMe;

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
        $add_days = 30;
        $from = date('Y-m-d h:i:s', strtotime('-'.$add_days.' day', strtotime(date("Y-m-d h:i:s"))));
        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        $pagar_me = new PagarMe();
        $orderDatamodel = $objectManager->get('Magento\Sales\Model\Order')->getCollection()
            ->setOrder('created_at','DESC')
            ->addFieldToFilter('created_at', array('from'=>$from));

        foreach($orderDatamodel as $orderDatamodel1) {
            $adictionalInfo = $orderDatamodel1->getPayment()->getAdditionalInformation();
            if(isset($adictionalInfo['pagarme_transactions_id'])){
                $status_pagarme =  $pagar_me->consultPymentPagarMe($adictionalInfo['pagarme_transactions_id']);
                $orderState = '';
                switch ($status_pagarme) {
                    case 'authorized':
                    case 'paid':
                         $orderState = \Magento\Sales\Model\Order::STATE_PROCESSING;
                        break;
                    case 'refunded':
                    case 'refused':
                        $orderState = \Magento\Sales\Model\Order::STATE_CANCELED;
                        break;
                    case 'waiting_payment':
                    case 'pending_refund':
                    case 'analyzing':
                    case 'processing':
                    case 'pending_review':
                        $orderState = \Magento\Sales\Model\Order::STATE_PENDING_PAYMENT;
                        break;
                }

                if($orderState){
                    $pagar_me->setLog('Cron::  order id: '.$orderDatamodel1->getIncrementId().' | status: '.$orderState);
                    $orderDatamodel1->setState($orderState)->setStatus($orderState)->save();
                }

            }
        }

    }
    public function getCoreConfig($valor)
    {
        $scopeConfig = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\Config\ScopeConfigInterface');
        return $scopeConfig->getValue($valor, \Magento\Store\Model\ScopeInterface::SCOPE_STORES);
    }
}
