<?php
namespace Blueskytechco\Instagram\Model\ResourceModel;


class Instagrampost extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	
	public function __construct(
		\Magento\Framework\Model\ResourceModel\Db\Context $context
	)
	{
		parent::__construct($context);
	}
	
	protected function _construct()
	{
		$this->_init('blueskytechco_instagram', 'id');
	}
	
}