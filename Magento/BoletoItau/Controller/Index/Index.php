<?php


namespace Magento\BoletoItau\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();


        if(isset($_REQUEST['create_boleto'])) {
            echo '<form action="https://shopline.itau.com.br/shopline/shopline.aspx" method="post" name="form" id="boleto-itau">
             <input type="hidden" name="DC" value="' . $_REQUEST['create_boleto'] . '" />
             </form><script>document.getElementById("boleto-itau").submit();</script>';
            exit;
        }

        $storeManager = $objectManager->get('Magento\Framework\App\RequestInterface');
        $data_post = $storeManager->getPost();

        $msg_retorno = '';
        if(isset($data_post['idPlataforma'])) {
            $order = $objectManager->create('Magento\Sales\Model\Order')->loadByIncrementId($data_post['idPlataforma']);
            switch ($data_post['status']) {
                case "paid":
                case "Aprovado":
                case "completed":
                case "Completo":
                    $orderState = \Magento\Sales\Model\Order::STATE_PROCESSING;
                    $msg_retorno = 'Notificação automática: O pagamento foi aprovado. | Numero do Pagamento: '.$data_post['idTransacao'].' | Status: '.$data_post['status'];
                    break;
                case "refunded":
                case "Estornado":
                    $orderState = \Magento\Sales\Model\Order::STATE_HOLDED;
                    $msg_retorno = 'Notificação automática: O pagamento foi Estornado. | Numero: '.$data_post['idTransacao'].' | Status: '.$data_post['status'];
                    break;
                case "canceled":
                case "Cancelado":
                    $orderState = \Magento\Sales\Model\Order::STATE_CANCELED;
                    $msg_retorno = 'Notificação automática: O pedido foi Cancelado. | Numero: '.$data_post['idTransacao'].' | Status: '.$data_post['status'];
                    break;
                default:
                    $orderState = \Magento\Sales\Model\Order::STATE_PAYMENT_REVIEW;
            }

            $order->addStatusToHistory($orderState,$msg_retorno);
            $order->getPayment()->setAdditionalInformation('status_itaushopline', $data_post['status']);
            $order->setState($orderState)->setStatus($orderState);
            $order->save();

            exit;
        }
    }

}
