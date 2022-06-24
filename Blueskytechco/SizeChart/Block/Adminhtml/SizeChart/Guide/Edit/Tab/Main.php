<?php
/**
 * Copyright 2021. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Blueskytechco\SizeChart\Block\Adminhtml\SizeChart\Guide\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic as GenericForm;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Store\Model\System\Store;

class Main extends GenericForm implements TabInterface
{
    /**
     * @var  \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $store;

	/**
	 * @var \Magento\Cms\Model\Wysiwyg\Config
	 */
	protected $_wysiwygConfig;

    /**
     * Main constructor.
     *
     * @param Store $store
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        Store $store,
        Context $context,
        Registry $registry,
		\Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        FormFactory $formFactory,
        array $data = []
    ) {
        $this->registry          = $registry;
		$this->_wysiwygConfig = $wysiwygConfig;
        $this->store             = $store;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Blueskytechco\SizeChart\Model\SizeChart\Guide $size_chart */
        $size_chart = $this->_coreRegistry->registry('size_chart');
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => 'Main',
                'class'  => 'fieldset-wide'
            ]
        );

        if ($size_chart->getId()) {
            $fieldset->addField(
                'entity_id',
                'hidden',
                ['name' => 'entity_id']
            );
        }
		$fieldset->addField(
            'name',
            'text',
            [
                'name'     => 'name',
                'label'    => __('Name'),
                'required' => true
            ]
        );
		if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'store_id',
                'select',
                [
                    'name' => 'store_id', 
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->store->getStoreValuesForForm(false, true)
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'store_id',
                'hidden',
                ['name' => 'store_id', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $size_chart->setStoreId($this->_storeManager->getStore(true)->getId());
        }
		$fieldset->addField(
            'type',
            'select',
            [
                'name'     => 'type',
                'label'    => __('Display'),
                'options'  => ['1' => __('Tab Content'), '2' => __('Popup Content')],
                'required' => true
            ]
        );
		$fieldset->addField(
			'content',
			'editor',
			[
				'name' => 'content',
				'label' => __('Edit Content'),
				'title' => __('Edit Content'),
				'rows' => '5',
				'cols' => '10',
				'wysiwyg' => true,
				'config' => $this->_wysiwygConfig->getConfig(),
				'required' => true
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
        return __('Main');
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
