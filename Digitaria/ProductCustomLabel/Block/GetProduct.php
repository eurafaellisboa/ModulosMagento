<?php
namespace Digitaria\ProductCustomLabel\Block;
 
use Magento\Framework\View\Element\Template;
 
class GetProduct extends Template
{
 
    protected $_registry;
 
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry
    )
    {
        $this->_registry = $registry;
        parent::__construct($context);
    }
 
    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }
 
}