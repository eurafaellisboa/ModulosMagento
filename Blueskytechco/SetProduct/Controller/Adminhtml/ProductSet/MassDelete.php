<?php

namespace Blueskytechco\SetProduct\Controller\Adminhtml\ProductSet;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Blueskytechco\SetProduct\Model\ResourceModel\ProductSet\CollectionFactory;

class MassDelete extends Action
{

    public $filter;
    public $collectionFactory;

    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        $this->filter            = $filter;
        $this->collectionFactory = $collectionFactory;

        parent::__construct($context);
    }

    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        try {
            $collection->walk('delete');
            $this->messageManager->addSuccessMessage(__('Labels has been deleted.'));
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('An unknown error.'));
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}
