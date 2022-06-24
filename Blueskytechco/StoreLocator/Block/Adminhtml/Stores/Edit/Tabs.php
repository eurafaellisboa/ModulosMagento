<?php
/**
 * Copyright © 2019 Blueskytechco. All rights reserved.
 */

namespace Blueskytechco\StoreLocator\Block\Adminhtml\Stores\Edit;

use \Blueskytechco\StoreLocator\Block\Adminhtml\Stores\Edit\Tab\Info;
use \Blueskytechco\StoreLocator\Block\Adminhtml\Stores\Edit\Tab\Map;
use \Blueskytechco\StoreLocator\Block\Adminhtml\Stores\Edit\Tab\TimeInfo;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('storelocator_stores_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Store Edit'));
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'store_info',
            [
                'label' => __('General Informations'),
                'title' => __('General Informations'),
                'content' => $this->getLayout()->createBlock(
                    Info::class
                )->toHtml(),
                'active' => true
            ]
        );
		
		$this->addTab(
            'time_info',
            [
                'label' => __('Work Time'),
                'title' => __('Work Time'),
                'content' => $this->getLayout()->createBlock(
                    TimeInfo::class
                )->toHtml(),
                'active' => false
            ] 
        );

        $this->addTab(
            'map_info',
            [
                'label' => __('Location Map'),
                'title' => __('Location Map'),
                'content' => $this->getLayout()->createBlock(
                    Map::class
                )->toHtml(),
                'active' => false
            ]
        );

        return parent::_beforeToHtml();
    }
}
