<?php
 
namespace Blueskytechco\SetProduct\Block\Adminhtml;
 
use Magento\Backend\Block\Widget\Grid\Container;
 
class ProductSet extends Container
{
   /**
     * Constructor
     *
     * @return void
     */
	protected function _construct()
    {
		$this->_controller = 'adminhtml_productSet';
        $this->_blockGroup = 'Blueskytechco_SetProduct';
        $this->_headerText = __('Add Lookbook');
        $this->_removeButtonLabel = __('Add Lookbook');
        parent::_construct();
    }
}
