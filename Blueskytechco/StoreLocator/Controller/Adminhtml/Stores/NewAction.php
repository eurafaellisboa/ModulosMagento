<?php
/**
 * Copyright © 2019 Blueskytechco. All rights reserved.
 */

namespace Blueskytechco\StoreLocator\Controller\Adminhtml\Stores;

use \Blueskytechco\StoreLocator\Controller\Adminhtml\Stores;
use \Magento\Backend\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;
use \Blueskytechco\StoreLocator\Api\StoreRepositoryInterface;
use \Blueskytechco\StoreLocator\Helper\Config as ConfigHelper;
use \Magento\Backend\Model\View\Result\ForwardFactory;
use \Blueskytechco\StoreLocator\Api\Data\StoreInterfaceFactory;

class NewAction extends Stores
{

    protected $resultForwardFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        StoreRepositoryInterface $storeRepository,
        StoreInterfaceFactory $storeFactory,
        ConfigHelper $configHelper,
        ForwardFactory $resultForwardFactory
    ) {
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context, $resultPageFactory, $storeRepository, $storeFactory, $configHelper);
    }


    public function execute()
    {
        if ($error = $this->checkGoogleApiKey()) {
            return $error;
        }
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}
