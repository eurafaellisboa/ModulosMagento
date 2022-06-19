<?php
namespace VimirLab\CategoryFee\Observer;

use Magento\Framework\Event\ObserverInterface;
use VimirLab\CategoryFee\Helper\Data as DataHelper;

class SaveOrderBeforeSalesModelQuoteObserver implements ObserverInterface
{
	protected $dataHelper; 
	
    public function __construct(
        DataHelper $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
    }
	
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
		$order = $observer->getEvent()->getData('order');
		if (!$this->dataHelper->isEnabled($order->getStoreId())) {
            return $this;
        }
        $quote = $observer->getEvent()->getData('quote');
		
		if ($quote->hasData('category_fee')) {
			$order->setCategoryFee($quote->getCategoryFee());
		}
		
		return $this;
    }
}