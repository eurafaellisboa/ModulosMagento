<?php
namespace Blueskytechco\ProductWidgetAdvanced\Model\ResourceModel;


class MostViewed extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	
	public function __construct(
		\Magento\Framework\Model\ResourceModel\Db\Context $context
	)
	{
		parent::__construct($context);
	}
	
	protected function _construct()
	{
		$this->_init('blueskytechco_product_index_most_viewed', 'id');
	}
	
}