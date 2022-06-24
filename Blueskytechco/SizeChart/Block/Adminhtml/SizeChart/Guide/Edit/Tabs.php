<?php
/**
 * Copyright © 2021. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Blueskytechco\SizeChart\Block\Adminhtml\SizeChart\Guide\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

class Tabs extends WidgetTabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('sizechart_category_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Information'));
    }
}
