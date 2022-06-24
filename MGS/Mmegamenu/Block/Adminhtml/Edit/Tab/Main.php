<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Mmegamenu\Block\Adminhtml\Edit\Tab;

/**
 * Sitemap edit form
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
	/**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
	
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
		\Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
		$this->_objectManager = $objectManager;
        parent::__construct($context, $registry, $formFactory, $data);
    }
	
	protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('mmegamenu_mmegamenu');

        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('mmegamenu_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('General Information')]);
		
		$data = $model->getData();

        if ($model->getId()) {
            $fieldset->addField('megamenu_id', 'hidden', ['name' => 'megamenu_id']);
        }
		
		$fieldset->addField('store', 'hidden', ['name' => 'store']);
		
		$data['store'] = 0;
		if($this->getRequest()->getParam('store')){
			$data['store'] = $this->getRequest()->getParam('store');
		}
		
		$menus = $this->_objectManager->create('MGS\Mmegamenu\Model\Parents')->getCollection();
		$menuOptions = [];
		if(count($menus)>0){
			foreach($menus as $_menu){
				$menuOptions[] = [
					'label' => $_menu->getTitle(),
					'value' => $_menu->getId()
				];
			}
		}
		
		$fieldset->addField(
            'parent_id',
            'select',
            [
                'label' => __('Menu'),
                'title' => __('Menu'),
                'name' => 'parent_id',
                'required' => true,
                'values' => $menuOptions
            ]
        );

        $fieldset->addField(
            'title',
            'text',
            [
                'label' => __('Label'),
                'name' => 'title',
                'required' => true,
                'value' => $model->getTitle()
            ]
        );
		
		$fieldset->addField(
            'menu_type',
            'select',
            [
                'label' => __('Menu Type'),
                'name' => 'menu_type',
                'required' => false,
                'options' => ['1' => __('Catalog Category'), '2' => __('Static Content')]
            ]
        );
		
		$fieldset->addField(
            'use_thumbnail',
            'select',
            [
                'label' => __('Show Thumbnail Image'),
                'name' => 'use_thumbnail',
                'required' => false,
                'options' => ['1' => __('Yes'), '0' => __('No')]
            ]
        );
		
		$fieldset->addField(
            'url',
            'text',
            [
                'label' => __('Link'),
                'name' => 'url',
                'value' => $model->getUrl(),
				'note' => __('Blank to use category link.')
            ]
        );
		
		$fieldset->addField(
            'position',
            'text',
            [
                'label' => __('Position'),
                'name' => 'position',
                'value' => $model->getPosition(),
				'class' => 'validate-number'
            ]
        );
		
		$fieldset->addField(
            'columns',
            'select',
            [
                'label' => __('Columns'),
                'name' => 'columns',
                'required' => false,
                'options' => [
					'1' => __('1'), 
					'2' => __('2'),
					'3' => __('3'),
					'4' => __('4'),
					'6' => __('6'),
				]
            ]
        );
		
		$fieldset->addField(
            'special_class',
            'text',
            [
                'label' => __('Custom Class'),
                'name' => 'special_class',
                'value' => $model->getSpecialClass()
            ]
        );
		
		$fieldset->addField(
            'html_label',
            'text',
            [
                'label' => __('Special Html'),
                'name' => 'html_label',
                'value' => $model->getHtmlLabel()
            ]
        );
		
		$fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'name' => 'status',
                'required' => false,
                'options' => ['1' => __('Enabled'), '0' => __('Disabled')]
            ]
        );
		
		
		/* Check is single store mode */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'store_id',
                'multiselect',
                [
                    'name' => 'stores[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->_systemStore->getStoreValuesForForm(false, true)
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
                ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }

        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }

	/**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('General Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('General Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
