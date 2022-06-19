<?php

namespace Hiddentechies\Neworder\Observer;

use Magento\Framework\Event\ObserverInterface;

class NewOrder implements ObserverInterface {

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Get extension configuration helper
     * @var \Hiddentechies\Testimonials\Helper\Config
     */
    public $dataHelper;
    
    /**
     * Pricing Helper
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    public $curHelper;
    
    /**
     * Sales order Model
     * @var \Magento\Sales\Model\Order
     */
    protected $order;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Hiddentechies\Neworder\Helper\Config $dataHelper
     */
    public function __construct(
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Model\Order $order,
        \Hiddentechies\Neworder\Helper\Data $dataHelper,
        \Magento\Framework\Pricing\Helper\Data $curHelper
    ) {
        $this->inlineTranslation = $inlineTranslation;
        $this->_storeManager = $storeManager;
        $this->_transportBuilder = $transportBuilder;
        $this->_scopeConfig = $scopeConfig;
        $this->order = $order;
        $this->dataHelper = $dataHelper;
        $this->curHelper = $curHelper;
    }

    protected function _sendEmail($from, $to, $templateId, $vars, $store, $area = \Magento\Framework\App\Area::AREA_FRONTEND) {
        $this->inlineTranslation->suspend();
        $this->_transportBuilder
                ->setTemplateIdentifier($templateId)
                ->setTemplateOptions([
                    'area' => $area,
                    'store' => $store->getId()
                ])
                ->setTemplateVars($vars)
                ->setFrom($from)
                ->addTo($to['email'], $to['name']);
        $transport = $this->_transportBuilder->getTransport();
        $transport->sendMessage();
        
        $this->inlineTranslation->resume();
        
        return true;
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $isNewOrderEnable = $this->dataHelper->getConfigVal('newordersection/generalgroup/enabled');
        // Check if new order email notification enabled
        if($isNewOrderEnable){
            
            // Set current order details
            $orderIds = $observer->getEvent()->getOrderIds();
            $order = $this->order->load($orderIds);
            $orderIncId = $order->getIncrementId();
            
            // Set email config options
            $store = $this->_storeManager->getStore();
            $from = $this->dataHelper->getConfigVal('newordersection/generalgroup/send_from');
            $to = [
                'email' => $this->dataHelper->getConfigVal('newordersection/generalgroup/email'),
                'name' => 'Administrator'
            ];
            $templateId = $this->dataHelper->getConfigVal('newordersection/generalgroup/template');
            $subject = $this->dataHelper->getConfigVal('newordersection/generalgroup/subject');

            // Set email template variables
            $vars = [
                'admin_subject' => $subject,
                'cur_order_id' => "#".$orderIncId,
            ];

            // Call send email function with the necessary parameters
            $this->_sendEmail($from, $to, $templateId, $vars, $store);
        }
    }
}