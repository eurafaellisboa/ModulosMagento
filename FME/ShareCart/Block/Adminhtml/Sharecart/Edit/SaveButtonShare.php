<?php
/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME
 * @author     Atta <support@fmeextensions.com>
 * @package   FME_Testimonials
 * @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
 * @license   https://fmeextensions.com/LICENSE.txt
 */
namespace FME\ShareCart\Block\Adminhtml\Sharecart\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class SaveButton
 * @package Magento\Customer\Block\Adminhtml\Edit
 */
class SaveButtonShare extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
        ];
    }
}