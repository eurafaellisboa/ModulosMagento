<?php
namespace VimirLab\CategoryFee\Block\Adminhtml\Sales\Order;

class CategoryFee extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \VimirLab\CategoryFee\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }

    public function displayFullSummary()
    {
        return true;
    }

    public function getSource()
    {
        return $this->_source;
    }

    public function getStore()
    {
        return $this->_order->getStore();
    }

    public function getOrder()
    {
        return $this->_order;
    }

    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }

    public function initTotals()
    {
		$totalSummaryLabel = $this->dataHelper->getTotalSummaryLabel();
		
        $parent = $this->getParentBlock();
        $this->_order = $parent->getOrder();
        $source = $parent->getSource();
		
        $categoryFee = 0;
		
		if ($source->hasData('category_fee')) {
			$categoryFee = $source->getCategoryFee();
            $totalSummaryLabel = $this->dataHelper->getTotalSummaryLabel($source->getStore()->getId());
		}

        if ($categoryFee > 0) {
            $feeData = new \Magento\Framework\DataObject(
                [
                    'code' => 'category_fee',
                    'strong' => false,
                    'value' => $categoryFee,
                    'label' => $totalSummaryLabel,
                ]
            );
            $parent->addTotalBefore($feeData, 'shipping');
        }

        return $this;
    }
}
