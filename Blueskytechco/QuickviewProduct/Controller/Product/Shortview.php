<?php
namespace Blueskytechco\QuickviewProduct\Controller\Product;

class Shortview extends \Magento\Catalog\Controller\Product\View
{
	public function execute()
    {
        $result = parent::execute();
        $layout = $result->getLayout();
        $block = $layout->renderElement('short.product.view');
        $this->getResponse()->setBody($block);
        return '';
    }
}
