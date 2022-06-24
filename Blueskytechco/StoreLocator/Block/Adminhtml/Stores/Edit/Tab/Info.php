<?php
/**
 * Copyright © 2019 Blueskytechco. All rights reserved.
 */

namespace Blueskytechco\StoreLocator\Block\Adminhtml\Stores\Edit\Tab;

use \Magento\Backend\Block\Widget\Form\Generic;
use \Magento\Backend\Block\Template\Context;
use \Magento\Framework\Registry;
use \Magento\Framework\Data\FormFactory;
use \Blueskytechco\StoreLocator\Model\System\Config\IsActive;
use \Blueskytechco\StoreLocator\Model\Config\Source\Country;

class Info extends Generic
{
    /**
     * @var IsActive
     */
    private $isActive;


    /**
     * @var Country
     */
    private $country;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        IsActive $isActive,
        Country $country,
        array $data = []
    ) {
        $this->isActive = $isActive;
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
            'base_fieldset',
            ['legend' => __('Informations')]
        );

        if ($model->getId()) {
            $selectField = $fieldset->addField(
                'store_id',
                'hidden',
                ['name' => 'store_id']
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
		$fieldset->addField(
            'des',
            'textarea',
            [
                'name'     => 'des',
                'label'    => __('Description'),
                'required' => false
            ]
        );

		$fieldset->addField(
            'is_active',
            'select',
            [
                'label'   => __('Status'),
                'title'   => __('Status'),
                'name'    => 'is_active',
                'options' => $this->isActive->toOptionArray()
            ]
        );
		
		$fieldset = $form->addFieldset(
            'address_fieldset',
            ['legend' => __('Address')]
        );
		
        $fieldset->addField(
            'address',
            'text',
            [
                'name'     => 'address',
                'label'    => __('Address'),
                'required' => true
            ]
        );

        $fieldset->addField(
            'city',
            'text',
            [
                'name'     => 'city',
                'label'    => __('City'),
                'required' => false
            ]
        );

        $fieldset->addField(
            'postcode',
            'text',
            [
                'name'     => 'postcode',
                'label'    => __('Zip Code'),
                'required' => false
            ]
        );

        $fieldset->addField(
            'country',
            'select',
            [
                'name'     => 'country',
                'label'    => __('Country'),
                'options'  => $this->country->toOptionArray(),
                'required' => true
            ]
        );

        $fieldset->addField(
            'email',
            'text',
            [
                'name'     => 'email',
                'label'    => __('E-mail'),
                'required' => false
            ]
        );

        $fieldset->addField(
            'phone',
            'text',
            [
                'name'     => 'phone',
                'label'    => __('Phone Number'),
                'required' => false
            ]
        );

        $fieldset->addField(
            'fax',
            'text',
            [
                'name'     => 'fax',
                'label'    => __('Fax'),
                'required' => false
            ]
        );

        $fieldset->addField(
            'website',
            'text',
            [
                'name'     => 'website', 
                'label'    => __('Website'),
                'required' => false
            ]
        );

		$fieldset = $form->addFieldset(
            'image_fieldset',
            ['legend' => __('Store Image')]
        );
		
		$data = $model->getData();
		
		$after_element_html = '';
		if(isset($data['image_store'])){
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface'); 
			$currentStore = $storeManager->getStore();
			$mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
			$image = json_decode($data['image_store']);
			if($image){
				$after_element_html = '<div style="margin-top:15px;"><a href="'.$mediaUrl.$image.'" target="_blank"><image src="'.$mediaUrl.$image.'" style="max-width:250px;" /></a><div>';
			}
		}
		$fieldset->addField(
            'image_stored',
            'file',
            array( 
                'name' => 'image_stored', 
                'label' => __('Store Image'),
                'title' => __('Store Image'),
                'note' => 'Allow image type: jpg, jpeg, gif, png',
				'after_element_html' => $after_element_html
            )
        );

        if (!$model->getId()) {
            $model->setData('is_active', '1');
        }

        
		
		if(isset($data['time_store'])){
			$time = json_decode($data['time_store']);
			$selectField->setAfterElementHtml('
				<script>
					require([
						"jquery"
					], function($){
						$(".monday .admin__select-time").val('.$time->monday_time.');

						$(".monday .admin__field-control-to .from-hours").val('.$time->monday->from->hours.');
						$(".monday .admin__field-control-to .from-minutes").val('.$time->monday->from->minutes.');
						$(".monday .admin__field-control-to .to-hours").val('.$time->monday->to->hours.');
						$(".monday .admin__field-control-to .to-minutes").val('.$time->monday->to->minutes.');
						
						$(".tuesday .admin__select-time").val('.$time->tuesday_time.');
						
						$(".tuesday .admin__field-control-to .from-hours").val('.$time->tuesday->from->hours.');
						$(".tuesday .admin__field-control-to .from-minutes").val('.$time->tuesday->from->minutes.');
						$(".tuesday .admin__field-control-to .to-hours").val('.$time->tuesday->to->hours.');
						$(".tuesday .admin__field-control-to .to-minutes").val('.$time->tuesday->to->minutes.');
						
						$(".wednesday .admin__select-time").val('.$time->wednesday_time.');
						
						$(".wednesday .admin__field-control-to .from-hours").val('.$time->wednesday->from->hours.');
						$(".wednesday .admin__field-control-to .from-minutes").val('.$time->wednesday->from->minutes.');
						$(".wednesday .admin__field-control-to .to-hours").val('.$time->wednesday->to->hours.');
						$(".wednesday .admin__field-control-to .to-minutes").val('.$time->wednesday->to->minutes.');
						
						$(".thursday .admin__select-time").val('.$time->thursday_time.');
						
						$(".thursday .admin__field-control-to .from-hours").val('.$time->thursday->from->hours.');
						$(".thursday .admin__field-control-to .from-minutes").val('.$time->thursday->from->minutes.');
						$(".thursday .admin__field-control-to .to-hours").val('.$time->thursday->to->hours.');
						$(".thursday .admin__field-control-to .to-minutes").val('.$time->thursday->to->minutes.');
						
						$(".friday .admin__select-time").val('.$time->friday_time.');
						
						$(".friday .admin__field-control-to .from-hours").val('.$time->friday->from->hours.');
						$(".friday .admin__field-control-to .from-minutes").val('.$time->friday->from->minutes.');
						$(".friday .admin__field-control-to .to-hours").val('.$time->friday->to->hours.');
						$(".friday .admin__field-control-to .to-minutes").val('.$time->friday->to->minutes.');
						
						$(".sturday .admin__select-time").val('.$time->saturday_time.');
						
						$(".sturday .admin__field-control-to .from-hours").val('.$time->saturday->from->hours.');
						$(".sturday .admin__field-control-to .from-minutes").val('.$time->saturday->from->minutes.');
						$(".sturday .admin__field-control-to .to-hours").val('.$time->saturday->to->hours.');
						$(".sturday .admin__field-control-to .to-minutes").val('.$time->saturday->to->minutes.'); 
						
						$(".sunday .admin__select-time").val('.$time->sunday_time.');
						
						$(".sunday .admin__field-control-to .from-hours").val('.$time->sunday->from->hours.');
						$(".sunday .admin__field-control-to .from-minutes").val('.$time->sunday->from->minutes.');
						$(".sunday .admin__field-control-to .to-hours").val('.$time->sunday->to->hours.');
						$(".sunday .admin__field-control-to .to-minutes").val('.$time->sunday->to->minutes.');
					});
				</script>
			');
		}
        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
