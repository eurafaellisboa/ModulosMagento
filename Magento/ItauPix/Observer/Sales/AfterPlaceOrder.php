<?php
namespace Magento\ItauPix\Observer\Sales;

use Magento\Framework\Event\ObserverInterface;

class AfterPlaceOrder implements ObserverInterface
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
    )
    {
        $this->order = $order;
        $this->_orderRepository = $orderRepository;
        $this->_invoiceService = $invoiceService;
        $this->_transaction = $transaction;
        $this->invoiceSender = $invoiceSender;
    
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {

           
    }
}

