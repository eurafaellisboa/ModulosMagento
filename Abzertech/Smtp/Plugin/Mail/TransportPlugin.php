<?php

namespace Abzertech\Smtp\Plugin\Mail;

use Closure;
use Magento\Framework\Mail\TransportInterface;
use Abzertech\Smtp\Helper\Data;
use Abzertech\Smtp\Model\Store;
use Abzertech\Smtp\Model\CoreFactory;
use Abzertech\Smtp\Model\Smtp  as ZendMailSmtp;

class TransportPlugin
{

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var Store
     */
    protected $storeModel;
    
    /**
     * @var CoreFactory
     */
    protected $coreFactory;

    /**
     * TransportPlugin constructor.
     *
     * @param Data $dataHelper
     * @param Store $storeModel
     */
    public function __construct(Data $dataHelper, Store $storeModel, CoreFactory $coreFactory)
    {
        $this->dataHelper = $dataHelper;
        $this->storeModel = $storeModel;
        $this->coreFactory = $coreFactory;
    }

    /**
     * Around Send Message.
     *
     * @param TransportInterface $subject
     * @param Closure $proceed
     */
    public function aroundSendMessage(TransportInterface $subject, Closure $proceed)
    {
        if ($this->dataHelper->isActive()) {
            if (method_exists($subject, 'getStoreId')) {
                $this->storeModel->setStoreId($subject->getStoreId());
            }
            $message = $subject->getMessage();
            $smtp = new ZendMailSmtp($this->dataHelper, $this->storeModel, $this->coreFactory);
            $smtp->sendSmtpMessage($message);
        } else {
            $proceed();
        }
    }
}
