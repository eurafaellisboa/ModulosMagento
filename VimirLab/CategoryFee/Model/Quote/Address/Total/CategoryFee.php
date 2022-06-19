<?php

namespace VimirLab\CategoryFee\Model\Quote\Address\Total;

use VimirLab\CategoryFee\Helper\Data as DataHelper;

class CategoryFee extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
	protected $dataHelper; 
	
    public function __construct(
        DataHelper $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
    }
	
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
		
        parent::collect($quote, $shippingAssignment, $total);
		if($this->dataHelper->isEnabled($quote->getStore()->getId())) {
			if (!$shippingAssignment->getItems()) {
				return $this;
			}
			
			$categoryFee = 0;
			foreach ($shippingAssignment->getItems() as $item) {
				$categoryFee += $item->getCategoryFee();
			}
			
			$total->addTotalAmount('category_fee', $categoryFee);
			$total->addBaseTotalAmount('category_fee', $categoryFee);
			$quote->setCategoryFee($categoryFee);
		}
        return $this;
    }
  
    public function fetch(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
		$storeId = $quote->getStore()->getId();
		if($this->dataHelper->isEnabled($storeId)) {
			$categoryFee = 0;
			foreach ($quote->getAllVisibleItems() as $item) {
				$categoryFee += $item->getCategoryFee();
			}
			
			return [
				'code' => 'category_fee',
				'title' => $this->dataHelper->getTotalSummaryLabel(),
				'value' => $categoryFee
			];
		}
    }
}