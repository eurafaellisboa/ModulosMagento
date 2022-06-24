<?php
/**
 * Copyright © 2019 Blueskytechco. All rights reserved.
 */

namespace Blueskytechco\StoreLocator\Block\Adminhtml\Stores\Edit\Tab;

use \Magento\Backend\Block\Widget\Form\Generic;
use \Magento\Backend\Block\Template\Context;
use \Magento\Framework\Registry;
use \Magento\Framework\Data\FormFactory;
use \Blueskytechco\StoreLocator\Model\Config\Source\Country;
use \Blueskytechco\StoreLocator\Block\Adminhtml\Stores\Helper\Monday;
use \Blueskytechco\StoreLocator\Block\Adminhtml\Stores\Helper\Tuesday;
use \Blueskytechco\StoreLocator\Block\Adminhtml\Stores\Helper\Wednesday;
use \Blueskytechco\StoreLocator\Block\Adminhtml\Stores\Helper\Thursday;
use \Blueskytechco\StoreLocator\Block\Adminhtml\Stores\Helper\Friday;
use \Blueskytechco\StoreLocator\Block\Adminhtml\Stores\Helper\Saturday;   
use \Blueskytechco\StoreLocator\Block\Adminhtml\Stores\Helper\Sunday;

class TimeInfo extends Generic
{
    /**
     * @var Country
     */
    private $country;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Country $country,
        array $data = []
    ) {
        $this->country = $country;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * View URL getter
     *
     * @param int $storeId
     *
     * @return string
     */
    public function getViewUrl($storeId)
    {
        return $this->getUrl('storelocator/*/*', ['store_id' => $storeId]);
    }

    /**
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('storelocator_store');

        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset(
            'time_fieldset',
            ['legend' => __('Localization informations')]
        );
		$fieldset->addType('monday', Monday::class);
		$fieldset->addType('tuesday', Tuesday::class);
		$fieldset->addType('wednesday', Wednesday::class);
		$fieldset->addType('thursday', Thursday::class);
		$fieldset->addType('friday', Friday::class);
		$fieldset->addType('saturday', Saturday::class);
		$fieldset->addType('sunday', Sunday::class);
		
		
		$fieldset->addField(
            'monday_location',
            'monday',
            [
                'name'  => 'monday_location',
                'label' => __('Monday'),
                'title' => __('Monday')
            ]
        );
		$fieldset->addField(
            'tuesday_location',
            'tuesday',
            [
                'name'  => 'tuesday_location',
                'label' => __('Tuesday'),
                'title' => __('Tuesday')
            ]
        );
		$fieldset->addField(
            'wednesday_location',
            'wednesday',
            [
                'name'  => 'wednesday_location',
                'label' => __('Wednesday'),
                'title' => __('Wednesday')
            ]
        );
		$fieldset->addField(
            'thursday_location',
            'thursday',
            [
                'name'  => 'thursday_location',
                'label' => __('Thursday'),
                'title' => __('Thursday')
            ]
        );
		$fieldset->addField(
            'friday_location',
            'friday',
            [
                'name'  => 'friday_location',
                'label' => __('Friday'),
                'title' => __('Friday')
            ]
        );
		$fieldset->addField(
            'saturday_location',
            'saturday',
            [
                'name'  => 'saturday_location',
                'label' => __('Saturday'),
                'title' => __('Saturday')
            ]
        );
		 
		$fieldset->addField(
            'sunday_location',
            'sunday',
            [
                'name'  => 'sunday_location',
                'label' => __('Sunday'),
                'title' => __('Sunday')
            ]
        );
        $data = $model->getData();
        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
