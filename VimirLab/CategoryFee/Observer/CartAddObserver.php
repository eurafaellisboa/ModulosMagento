<?php

namespace VimirLab\CategoryFee\Observer;

use Magento\Framework\Event\ObserverInterface;
use VimirLab\CategoryFee\Helper\Data as DataHelper;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Checkout\Model\Session as CheckoutSession;

class CartAddObserver implements ObserverInterface
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
	    $categoryIds = [];
	    $quoteItems = $this->checkoutSession->getQuote()->getAllVisibleItems();
	    if(!empty($quoteItems) && count($quoteItems) > 0) {
            foreach($quoteItems as $quoteItem) {
                if($quoteItem->getCategoryIds()) {
                    $categoryIds [] = $quoteItem->getCategoryIds();
                }
            }
         }
		return array_filter($categoryIds);
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
		$categoryIds = $product->getCategoryIds();
		if(!empty($categoryIds) && count($categoryIds) > 0) {
			$categoryFee = $this->_setCategoryFee($categoryIds, $storeId);
			$item->setCategoryFee($categoryFee);
			$item->setCategoryIds(implode(',', $categoryIds));
		}
        return $this; 
	}
	
	protected function _setCategoryFee($categoryIds, $storeId) 
	{
		$categoryFee = 0;
		foreach(array_unique($categoryIds) as $categoryId) {
			$category = $this->getCategory($categoryId, $storeId);
			if($category && $category->getId() && $category->getIsActive() && $category->getCategoryFee() > 0) {
			    $categoryId = $category->getId();
                $itemCategories = $this->getItemCategories();
			    if(!in_array(trim($categoryId), $itemCategories)) {
			        $categoryFee += $category->getCategoryFee();
			    }
			}
		}
		return $categoryFee;
	}
}