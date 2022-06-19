<?php

namespace Abzertech\Smtp\Model;

use Abzertech\Smtp\Helper\Data;
use Abzertech\Smtp\Model\Store;
use Abzertech\Smtp\Model\CoreFactory;

abstract class AbstractSmtp
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
     *
     * @var Escaper
     */
    protected $escaper;

    /**
     * @param Data $dataHelper
     * @param Store $storeModel
     * @param CoreFactory $coreFactory
     */
    public function __construct(
        Data $dataHelper,
        Store $storeModel,
        CoreFactory $coreFactory
    ) {
        $this->dataHelper = $dataHelper;
        $this->storeModel = $storeModel;
        $this->coreFactory = $coreFactory;
        $this->escaper = new \Magento\Framework\Escaper;
    }

    /**
     * @param Data $dataHelper
     * @return Smtp
     */
    public function setDataHelper(Data $dataHelper)
    {
        $this->dataHelper = $dataHelper;
        return $this;
    }

    /**
     * @param Store $storeModel
     * @return Smtp
     */
    public function setStoreModel(Store $storeModel)
    {
        $this->storeModel = $storeModel;
        return $this;
    }
    
    public function addLog(array $data)
    {
        if ($this->dataHelper->isLogEnabled()) {
            $model = $this->coreFactory->create();
            $model->addData($data);
            $model->save();
        }
    }

    /**
     * @param Magento\Framework\Mail\EmailMessageInterface $message
     * @throws MailException
     */
    abstract public function sendSmtpMessage($message);
}
