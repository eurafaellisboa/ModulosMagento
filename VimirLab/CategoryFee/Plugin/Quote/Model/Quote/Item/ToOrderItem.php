<?php
namespace VimirLab\CategoryFee\Plugin\Quote\Model\Quote\Item;

use Closure;
use VimirLab\CategoryFee\Helper\Data as DataHelper;

class ToOrderItem
{
	protected $dataHelper; 
	
    public function __construct(
        DataHelper $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
    }
	
	public function aroundConvert(
		\Magento\Quote\Model\Quote\Item\ToOrderItem $subject,
		Closure $proceed,
		\Magento\Quote\Model\Quote\Item\AbstractItem $item,
		$additional = []
	) {
	   
		$orderItem = $proceed($item, $additional);
		
		$storeId = $item->getStore()->getId();
		if (!$this->dataHelper->isEnabled($storeId)) {
            return $orderItem;
        }
		
		if ($item->hasData('category_fee')) {
			$orderItem->setCategoryFee($item->getCategoryFee());
		}
		
		return $orderItem;
	}

}