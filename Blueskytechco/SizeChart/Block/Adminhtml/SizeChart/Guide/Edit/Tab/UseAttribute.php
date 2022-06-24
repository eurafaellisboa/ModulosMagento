<?php
/**
 * Copyright © 2021. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Blueskytechco\SizeChart\Block\Adminhtml\SizeChart\Guide\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic as GenericForm;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class UseAttribute extends GenericForm implements TabInterface
{

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Blueskytechco\SizeChart\Model\SizeChart\Guide $size_chart */
        $size_chart = $this->_coreRegistry->registry('size_chart');

        $form     = $this->_formFactory->create();
        $form->setHtmlIdPrefix('size_chart');
        $form->setFieldNameSuffix('size_chart');
        $fieldset = $form->addFieldset('base_fieldset', [
            'legend'=>__('Use Attribute'),
            'class' => 'fieldset-wide']);
		$fieldset->addField(
			'use_attribute', 
			'multiselect',
			[
				'name' => 'use_attribute',
				'label' => __('Attribute'),
				'title' => __('Attribute'),
                'values' => $this->getValueArray(),
				'required' => false
			]
		);

        $form->addValues($size_chart->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Use Attribute');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

}
