<?php
namespace VimirLab\CategoryFee\Model\Sales\Totals;

use VimirLab\CategoryFee\Helper\Data as DataHelper;
use Magento\Sales\Model\Order\ItemRepository;

class Invoice extends \Magento\Sales\Model\Order\Invoice\Total\AbstractTotal
{
    public function __construct(
		ItemRepository $itemRepository,
		DataHelper $dataHelper,
        array $data = []
    ) {
        $this->itemRepository = $itemRepository;
		$this->dataHelper = $dataHelper;
        parent::__construct($data);
    }

    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        parent::collect($invoice);
		
		$storeId = $invoice->getStoreId();
		if($this->dataHelper->isEnabled($storeId)) {
			$baseTotal = $invoice->getBaseGrandTotal();
			$total = $invoice->getGrandTotal();
			$orderId = $invoice->getOrderId();
			
			$categoryFee = 0;
			foreach($invoice->getItemsCollection() as $item){
				$orderItem = $this->itemRepository->get($item->getOrderItemId());
				if($orderItem->hasData('category_fee') && $orderItem->getCategoryFee() > 0) {
					$categoryFee += $orderItem->getCategoryFee();
				}
			}
			
			$baseTotalCategoryFee = $totalCategoryFee = $categoryFee;

			$invoice
				->setBaseCategoryFee($baseTotalCategoryFee)
				->setCategoryFee($totalCategoryFee)
				->setBaseGrandTotal($invoice->getBaseGrandTotal() + $baseTotalCategoryFee)
				->setGrandTotal($invoice->getGrandTotal() + $totalCategoryFee);
		
		}
        return $this;
    }
}
