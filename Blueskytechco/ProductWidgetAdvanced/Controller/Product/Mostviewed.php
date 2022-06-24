<?php
namespace Blueskytechco\ProductWidgetAdvanced\Controller\Product;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class Mostviewed extends Action
{
    private $resultJsonFactory;
    protected $_modelMostViewed;
    protected $_storeManager;

    public function __construct(JsonFactory $resultJsonFactory, Context $context, \Blueskytechco\ProductWidgetAdvanced\Model\MostViewedFactory $modelMostViewed, \Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_modelMostViewed = $modelMostViewed;
        $this->_storeManager = $storeManager;
    }
 
    public function execute()
    {
        $pr_id = $this->getRequest()->getPost('product_id');
        $store_id = $this->_storeManager->getStore()->getId();
        if($store_id){
            $create_model_most_view = $this->_modelMostViewed->create();
            $create_model_most_view->setStoreId($store_id);
            $create_model_most_view->setProductId($pr_id);
            $create_model_most_view->save();
        }
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData(['result' => 'done!']);
    }
}