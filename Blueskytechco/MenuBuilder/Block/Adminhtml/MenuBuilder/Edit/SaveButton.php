<?php
/**
 * Copyright © 2021 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Blueskytechco\MenuBuilder\Block\Adminhtml\MenuBuilder\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Blueskytechco\MenuBuilder\Block\Adminhtml\Edit\GenericButton;

class SaveButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        if ($this->getEntityId()) {
            return [
                'label' => __('Save'),
                'class' => 'save primary',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => ['event' => 'save'],
                    ],
                ],
                'sort_order' => 90
            ];
        } else {
            return [
                'label' => __('Create Menu'),
                'class' => 'save primary',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => ['event' => 'save'],
                    ],
                ],
                'sort_order' => 90
            ];
        }
    }
}
