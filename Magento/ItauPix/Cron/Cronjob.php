<?php


namespace Magento\ItauPix\Cron;

use Magento\ItauPix\Model\ItauPix;

class Cronjob
{
    /**
     * Order Model
     *
     * @var \Magento\Sales\Model\Order $order
     */
    protected $order;
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $_orderRepository;

    /**
     * @var \Magento\Sales\Model\Service\InvoiceService
     */
    protected $_invoiceService;

    /**
     * @var \Magento\Framework\DB\Transaction
     */
    protected $_transaction;

    /**
     * @var \Magento\Framework\DB\Transaction
     */
    protected $invoiceSender;

    public function __construct(
        \Magento\Sales\Model\Order $order,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\Transaction $transaction,
        \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender
    ) {
        $this->order = $order;
        $this->_orderRepository = $orderRepository;
        $this->_invoiceService = $invoiceService;
        $this->_transaction = $transaction;
        $this->invoiceSender = $invoiceSender;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        $add_days = 30;
        $from = date('Y-m-d h:i:s', strtotime('-' . $add_days . ' day', strtotime(date("Y-m-d h:i:s"))));
        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        $itaupix = new ItauPix();
        $orderDatamodel = $objectManager->get('Magento\Sales\Model\Order')->getCollection()
            ->setOrder('created_at', 'DESC')
            ->addFieldToFilter('created_at', ['from' => $from])
            ->addFieldToFilter('status', ['neq' => 'closed'])
            ->addFieldToFilter('status', ['neq' => 'complete'])
            ->addFieldToFilter('status', ['neq' => 'canceled']);

        foreach ($orderDatamodel as $orderDatamodel1) {
            if ($orderDatamodel1->hasInvoices()) {
                continue;
            }
            $adictionalInfo = $orderDatamodel1->getPayment()->getAdditionalInformation();
            if (isset($adictionalInfo['itaupix_transactions_txid'])) {
                $status_itaupix =  $itaupix->consultPymentItauPix($adictionalInfo['itaupix_transactions_txid']);

                // if ($orderDatamodel1->getIncrementId() == '000000110'){  // test 
                //     $status_itaupix = 'ATIVA';
                // }

                $orderState = '';
                $orderStatus = '';
                switch ($status_itaupix) {
                    case 'CONCLUIDA':
                        $orderState = \Magento\Sales\Model\Order::STATE_PROCESSING;
                        $orderStatus = $orderState;
                        break;
                    case 'NAO_REALIZADO':
                    case 'DEVOLVIDO':
                    case 'REMOVIDA_PELO_USUARIO_RECEBEDOR':
                    case 'REMOVIDA_PELO_PSP':
                        $orderState = \Magento\Sales\Model\Order::STATE_CANCELED;
                        $orderStatus = $orderState;
                        $orderDatamodel1->cancel();
                        break;
                    case 'ATIVA':
                    case 'EM_PROCESSAMENTO':
                        $orderStatus = 'pending';
                        $orderState = 'new';
                        break;
                }
                $itaupix->setLog('Cron::  order id: ' . $orderDatamodel1->getIncrementId() . ' | status magento: ' . $orderState . ' | status itaupix: ' . $status_itaupix);
                if ($orderState  && $orderDatamodel1->getState() != $orderState) {
                    $orderDatamodel1->setState($orderState)->setStatus($orderStatus)->save();
                }

                if ($orderDatamodel1->getStatus() == \Magento\Sales\Model\Order::STATE_PROCESSING) {
                    if (in_array($orderDatamodel1->getPayment()->getMethod(), ['itaupixbl', 'itaupix'])) {
                        $this->createInvoicer($orderDatamodel1->getId());
                    }
                }
            }
        }
    }
    public function getCoreConfig($valor)
    {
        $scopeConfig = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\Config\ScopeConfigInterface');
        return $scopeConfig->getValue($valor, \Magento\Store\Model\ScopeInterface::SCOPE_STORES);
    }

    public function createInvoicer($orderId)
    {
        $order = $this->order->load($orderId);

        if (!$order->hasInvoices()) {
            $invoice = $this->_invoiceService->prepareInvoice($order);
            $invoice->register();
            $invoice->pay();
            $invoice->save();
            $transactionSave = $this->_transaction->addObject(
                $invoice->getOrder()
            );
            $transactionSave->save();
            $this->invoiceSender->send($invoice);
            //send notification code
            $order->addStatusHistoryComment(
                __('Notified customer about invoice #%1.', $invoice->getId())
            )
                ->setIsCustomerNotified(true)
                ->save();
        }
    }
}
