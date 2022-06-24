<?php
/**
 * Copyright © 2019 Blueskytechco. All rights reserved.
 */

namespace Blueskytechco\StoreLocator\Block\Adminhtml\Stores;

use \Magento\Backend\Block\Widget\Form\Container;
use \Magento\Backend\Block\Widget\Context;
use \Magento\Framework\Registry;

class Edit extends Container
{
    
    private $coreRegistry;

   
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

   
    protected function _construct()
    {
        $this->_objectId = 'store_id';
        $this->_blockGroup = 'Blueskytechco_StoreLocator';
        $this->_controller = 'adminhtml_stores';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save Store'));
        $this->buttonList->add(
            'saveandcontinue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'saveAndContinueEdit',
                            'target' => '#edit_form'
                        ]
                    ]
                ]
            ],
            -100
        );
        $this->buttonList->update('delete', 'label', __('Delete Store'));
    }

    
    public function getHeaderText()
    {
        if ($this->coreRegistry->registry('storelocator_store')->getId()) {
            return __("Edit Store '%1'", $this->escapeHtml($this->coreRegistry->registry('storelocator_store')->getName()));
        } else {
            return __('Add Store');
        }
    }
}
