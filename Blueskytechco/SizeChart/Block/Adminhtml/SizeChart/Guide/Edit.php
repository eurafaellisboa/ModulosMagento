<?php
/**
 * Copyright © 2021. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Blueskytechco\SizeChart\Block\Adminhtml\SizeChart\Guide;

use Magento\Backend\Block\Widget\Form\Container as FormContainer;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

class Edit extends FormContainer
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
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
        $this->_objectId   = 'sizechart_id';
        $this->_blockGroup = 'Blueskytechco_SizeChart';
        $this->_controller = 'adminhtml_sizeChart_guide';
        parent::_construct();
		$this->addButton(
            'delete',
            [
                'label' => __('Delete'),
                'onclick' => 'deleteConfirm(' . json_encode(__('Are you sure you want to do this?'))
                    . ','
                    . json_encode($this->getDeleteUrl()
                    )
                    . ')',
                'class' => 'scalable delete',
                'level' => -1
            ]
        );
        $this->buttonList->update('save', 'label', __('Save Size Chart'));
		
    }

    public function getHeaderText()
    {
        $temlate = $this->coreRegistry->registry('size_chart');
        if ($temlate && $temlate->getId()) {
            return __("Edit Size Chart '%1'", $this->escapeHtml($temlate->getName()));
        }
        return __('New Size Chart');
    }
	
	/**
	 * @param array $args
	 * @return string
	 */
	public function getDeleteUrl(array $args = [])
	{
		return $this->getUrl('sizechart/*/delete', ['id' => $this->getRequest()->getParam('id')]);
	}
}
