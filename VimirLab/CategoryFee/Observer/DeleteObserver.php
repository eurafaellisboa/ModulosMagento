<?php

namespace VimirLab\CategoryFee\Observer;

use Magento\Framework\Event\ObserverInterface;
use VimirLab\CategoryFee\Helper\Data as DataHelper;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Checkout\Model\Session as CheckoutSession;

class DeleteObserver implements ObserverInterface
{
	protected $dataHelper; 
	protected $category = null; 
	
    public function __construct(
        DataHelper $dataHelper,
		CategoryRepository $categoryRepository,
		CheckoutSession $checkoutSession
    ) {
        $this->dataHelper = $dataHelper;
        $this->categoryRepository = $categoryRepository;
        $this->checkoutSession = $checkoutSession;
    }
	
	public function getItemCategories()
	{
	    $categoryItems = [];
	    $quoteItems = $this->checkoutSession->getQuote()->getAllVisibleItems();
	    if(!empty($quoteItems) && count($quoteItems) > 0) {
            foreach($quoteItems as $quoteItem) {
                if($quoteItem->getCategoryIds()) {
                    $categoryItems[$quoteItem->getCategoryIds()][] = $quoteItem;
                }
            }
         }
		return $categoryItems;
	}
	
	public function getCategory($categoryId, $storeId)
    {
		if ($categoryId) {
            try {
                $this->category = $this->categoryRepository->get($categoryId, $storeId);
            } catch (NoSuchEntityException $e) {
                $this->category = null;
            }
        }
        return $this->category;
    }
	
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$item = $observer->getEvent()->getQuoteItem();
		$product = $item->getProduct();
		$storeId = $product->getStoreId();
		if (!$this->dataHelper->isEnabled($storeId)) {
            return $this;
        }
        $categoryIds = [];
		$quoteItems = $this->getItemCategories();
	    if(!empty($quoteItems) && count($quoteItems) > 0) {
	        $isCheckFee = [];
            foreach($quoteItems as $key => $quoteItem) {
                foreach($quoteItem as $itemValue) {
                    if($item->getId() == $itemValue->getId()) {
                        continue;
                    }
                    if(!empty($itemValue->getCategoryIds()) && $itemValue->getCategoryFee() > 0) {
                        $isCheckFee[$key] = true;
                        break;
                    }
                }
                foreach($quoteItem as $itemValue) {
                    if($item->getId() == $itemValue->getId()) {
                        continue;
                    }
                    if(!isset($isCheckFee[$key])) {
                        $categoryId = $itemValue->getCategoryIds();
                        $categoryFee = $this->_setCategoryFee($categoryId, $storeId);
                        $itemValue->setCategoryFee($categoryFee);
                        $itemValue->save();
                        $isCheckFee[$key] = true;
                        break;
                    }
                }   
            } 
         }
        return $this; 
	}
	
	protected function _setCategoryFee($categoryId, $storeId) 
	{
		$categoryFee = 0;
        $category = $this->getCategory($categoryId, $storeId);
        if($category && $category->getId() && $category->getIsActive() && $category->getCategoryFee() > 0) {
            $categoryFee = $category->getCategoryFee();
        }
		return $categoryFee;
	}
}