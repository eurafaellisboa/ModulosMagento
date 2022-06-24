<?php
namespace Blueskytechco\SetProduct\Block\Adminhtml\Field;

use Magento\Backend\Block\Widget\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

class Tabs extends Container
{
    protected $_coreRegistry = null;
 
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $this->_controller = 'adminhtml_productSet';
        $this->_blockGroup = 'Blueskytechco_SetProduct'; 
 
        parent::_construct();

        $this->addButton(
            'back',
            [
                'label' => __('Back'),
                'onclick' => 'setLocation(\'' . $this->getUrl('addproductsset/productset/index') . '\')',
                'class' => 'back'
            ],
            -1
        );

        $this->addButton('save', [
            'label' => __('Save'),
            'class' => 'save primary',
            'id' => 'click-save-lookbook'
        ]);
    }

    public function getHeaderText()
    {
        $rule = $this->_coreRegistry->registry('blueskytechco_setproduct');
        return __('Lookbook');
    }

    public function getSaveUrl()
    {
        return $this->getFormActionUrl();
    }
    
    public function getFormActionUrl()
    {
        $rule = $this->_coreRegistry->registry('blueskytechco_setproduct');

        if ($id = $rule->getId()) {
            return $this->getUrl('*/*/save', ['id' => $id]);
        }

        return parent::getFormActionUrl();
    }
}